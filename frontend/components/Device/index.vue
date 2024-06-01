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
        <v-container class="mt-4">
          <!-- <v-row v-for="(item, index) in devices" :key="index">
            <v-col cols="6">
              <v-text-field
                v-model="item.model_number"
                dense
                outlined
                hide-details
                label="Device Model *"
              ></v-text-field>
            </v-col>

            <v-col cols="6">
              <v-text-field
                v-model="item.device_id"
                dense
                outlined
                hide-details
                label="Device Serial *"
              ></v-text-field>
            </v-col>
          </v-row> -->
          <v-row>
            <v-col cols="12" class="text-rightd">
              <v-icon @click="addItem" color="blue">
                mdi-plus-circle-outline
              </v-icon>
              <!-- <v-icon v-if="devices && devices.length > 1" @click="removeItem">
                mdi-delete
              </v-icon> -->
            </v-col>
            <v-col cols="12">
              <table>
                <tr>
                  <th>Device Model</th>
                  <th>Device Serial</th>
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
              <v-btn color="blue" dark @click="submit"> Submit </v-btn>

              <!-- <v-btn color="blue" dark @click="Decrypt"> Decrypt </v-btn> -->
            </v-col>
            <!-- <v-col cols="12">
              <pre>{{ item.devices }}</pre>
            </v-col> -->
            <!-- <v-col cols="12">
              <v-card outlined style="overflow: scroll">
                <v-card-title> Encrypted Value: </v-card-title>

                <v-card-text>
                  {{ encryptedData }}
                </v-card-text>
              </v-card>
            </v-col> -->
            <!-- <v-col cols="12">
              <v-card outlined style="overflow: scroll">
                <v-card-title> Decrypted Value: </v-card-title>
                <v-card-text>
                  {{ decryptedData }}
                </v-card-text>
              </v-card>
            </v-col> -->
          </v-row>
        </v-container>
      </v-card-text>
    </v-card>
  </v-dialog>
</template>
<script>
import { encryptData, decryptData } from "../../utils/encryption";

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
    let devices = this.item.devices;

    if (devices.length) {
      this.devices = devices;
    }

    this.loading = true;
    try {
      await this.$axios.get("device", this.item.id);
      this.close();
      this.$emit("response", "Record has been inserted");
    } catch (error) {
      this.errorResponse = error?.response?.data?.message || "Unknown error";
      this.loading = false;
    }
  },
  methods: {
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
          company_id: this.item.id,
        })),
      };

      this.loading = true;
      try {
        await this.$axios.post("device", this.payload);
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
        this.close();
        this.$emit("response", "Record has been inserted");
      } catch (error) {
        this.errorResponse = error?.response?.data?.message || "Unknown error";
        this.loading = false;
      }
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
    removeItem() {
      this.devices.pop();
    },
  },
};
</script>
<style scoped>
/* tableStyles.scss or tableStyles.css */
table {
  font-family: arial, sans-serif;
  border-collapse: collapse;
  width: 100%;
}

td,
th {
  border: 1px solid #dddddd;
  text-align: left;
  padding: 8px;
}
</style>
