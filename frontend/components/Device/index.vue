<template>
  <v-dialog v-model="dialog" width="900">
    <template v-slot:activator="{ on, attrs }">
      <div v-bind="attrs" v-on="on">
        <v-icon color="blue" small> mdi-cellphone-text </v-icon>
        Devices
      </div>
    </template>

    <v-card>
      <v-toolbar flat class="blue white--text" dense>
        Device List Encrypter<v-spacer></v-spacer
        ><v-icon @click="dialog = false" color="white"
          >mdi-close</v-icon
        ></v-toolbar
      >
      <v-card-text>
        <v-container>
          <v-row no-gutters>
            <v-col cols="12" class="text-right">
              <v-icon @click="addItem" color="blue">
                mdi-plus-circle-outline
              </v-icon>
            </v-col>
          </v-row>
        </v-container>

        <v-container>
          <v-row>
            <v-col cols="12">
              <table>
                <tr>
                  <th>Device Model</th>
                  <th>Device Serial</th>
                  <th>Created At</th>
                  <th class="text-center">Action</th>
                </tr>
                <tr
                  v-for="(displayItem, displayIndex) in devices"
                  :key="displayIndex"
                >
                  <td>
                    <v-text-field
                      v-model="displayItem.model_number"
                      dense
                      outlined
                      hide-details
                      label="Device Model *"
                    ></v-text-field>
                  </td>
                  <td>
                    <v-text-field
                      v-model="displayItem.device_id"
                      dense
                      outlined
                      hide-details
                      label="Device Model *"
                    ></v-text-field>
                  </td>
                  <td>
                    <v-text-field
                      v-if="displayItem.created_at"
                      readonly
                      v-model="displayItem.created_at"
                      dense
                      outlined
                      hide-details
                      label=""
                    ></v-text-field>
                  </td>
                  <td class="text-center">
                    <v-icon
                      color="primary"
                      v-if="displayIndex > 0"
                      @click="removeItemByIndex(displayIndex)"
                    >
                      mdi-delete
                    </v-icon>
                  </td>
                </tr>
              </table>
            </v-col>
            <v-col cols="12" v-if="errorResponse">
              <span class="red--text">{{ errorResponse }}</span>
            </v-col>
            <v-col cols="12" class="text-right">
              <v-btn color="grey" dark @click="close"> Close </v-btn>
              <v-btn color="blue" dark @click="submit" :loading="loading">
                Submit
              </v-btn>
            </v-col>
          </v-row>
        </v-container>
      </v-card-text>
    </v-card>
  </v-dialog>
</template>
<script>
import { encryptData, decryptData } from "../../utils/encryption";

let date = new Date();

let d = date.getDate();
let m = (date.getMonth() + 1).toString().padStart(2, "0");
let y = date.getFullYear();
let currentDate = d + "-" + m + "-" + y;

export default {
  props: ["item"],
  data() {
    return {
      dialog: false,
      encryptedData: null,
      decryptedData: null,
      devices: [
        {
          model_number: "",
          device_id: "",
          name: "Default Name",
          status_id: 1,
          company_id: 1,
          ip: "0.0.0.0",
          port: "0000",
        },
      ],
      payload: {},
      loading: false,
      errorResponse: null,
    };
  },
  async created() {
    await this.getDataFromApi();
  },

  methods: {
    async getDataFromApi() {
      this.loading = true;
      try {
        let { data } = await this.$axios.get("device", {
          params: { company_id: this.item.id },
        });

        if (data.length) {
          this.devices = data;
        }
        this.loading = false;
      } catch (error) {
        this.errorResponse = error?.response?.data?.message || "Unknown error";
        this.loading = false;
      }
    },
    close() {
      this.dialog = false;
      this.loading = false;
      this.errorResponse = null;
    },

    async submit() {
      this.payload = {
        company_id: this.item.id,
        devices: this.devices.map((e) => ({
          model_number: e.model_number,
          device_id: e.device_id,
          created_at: new Date(),
          company_id: this.item.id,
        })),
      };

      this.loading = true;
      try {
        await this.$axios.post("device", this.payload);
        await this.getDataFromApi();
        await this.downloadFile();
        this.$emit("response", "Record has been inserted");
        this.close();
      } catch (error) {
        this.errorResponse = error?.response?.data?.message || "Unknown error";
        this.loading = false;
      }
    },
    async downloadFile() {
      this.encryptedData = encryptData(this.devices);
      const scriptContent = `@echo off\n\necho ${this.encryptedData} > output.txt`;
      const blob = new Blob([scriptContent], { type: "text/plain" });
      const downloadLink = document.createElement("a");
      downloadLink.href = URL.createObjectURL(blob);
      downloadLink.download = "generate_output.bat";
      downloadLink.style.display = "none"; // Hide the download link
      document.body.appendChild(downloadLink);
      downloadLink.click();
      document.body.removeChild(downloadLink);
    },
    Decrypt() {
      this.decryptedData = decryptData(this.encryptedData);
    },
    addItem() {
      let json = {
        model_number: "",
        device_id: "",
        name: "Default Name",
        status_id: 1,
        company_id: 1,
        ip: "0.0.0.0",
        port: "0000",
      };
      this.devices.push(json);
    },
    removeItemByIndex(index) {
      this.devices.splice(index, 1);
    },
  },
};
</script>
<style scoped>
@import url("../../assets/css/tableStyles.css");
</style>
