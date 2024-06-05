<template>
  <v-dialog v-model="dialog" width="1300">
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
        <v-container fluid>
          <v-row no-gutters>
            <v-col cols="12" class="text-right">
              <v-icon @click="addItem" color="blue">
                mdi-plus-circle-outline
              </v-icon>
            </v-col>
          </v-row>
        </v-container>
      </v-card-text>

      <v-container fluid>
        <v-row>
          <v-col cols="12">
            <table>
              <tr>
                <td><small>Timezone</small></td>
                <td><small>Function</small></td>
                <td><small>Type</small></td>
                <td><small>Model</small></td>
                <td><small>Serial</small></td>
                <td><small>Created At</small></td>
                <td class="text-center"><small>Action</small></td>
              </tr>
              <tr
                v-for="(displayItem, displayIndex) in devices"
                :key="displayIndex"
              >
                <td>
                  <v-autocomplete
                    :hide-details="true"
                    v-model="displayItem.utc_time_zone"
                    placeholder="Time Zone"
                    outlined
                    dense
                    label="Timezone *"
                    :items="getTimezones()"
                    item-value="key"
                    item-text="text"
                  ></v-autocomplete>
                </td>
                <td>
                  <v-autocomplete
                    :hide-details="true"
                    v-model="displayItem.function"
                    placeholder="Function"
                    outlined
                    dense
                    label="Function *"
                    :items="[
                      { id: 'auto', name: 'Auto' },
                      { id: 'In', name: 'In' },
                      { id: 'Out', name: 'Out' },
                    ]"
                    item-value="id"
                    item-text="name"
                  ></v-autocomplete>
                </td>
                <td>
                  <v-autocomplete
                    :hide-details="true"
                    v-model="displayItem.device_type"
                    placeholder="Type"
                    outlined
                    dense
                    label="Type *"
                    :items="[
                      { id: 'all', name: 'All(Attendance and Access)' },
                      { id: 'Attendance', name: 'Attendance' },
                      { id: 'Access Control', name: 'Access Control' },
                    ]"
                    item-value="id"
                    item-text="name"
                  ></v-autocomplete>
                </td>
                <td>
                  <v-autocomplete
                    :hide-details="true"
                    v-model="displayItem.model_number"
                    placeholder="Time Zone"
                    outlined
                    dense
                    label="Device Model *"
                    :items="[`OX-866`, `OX-886`, `OX-966`, `OX-900`]"
                  ></v-autocomplete>
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
          name: "Default Name",
          utc_time_zone: "Asia/Dubai",
          function: "auto",
          device_type: "all",
          model_number: "OX-866",
          device_id: "",
          company_id: 1,
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
    getTimezones() {
      let timezones = require("../../jsons/utc_timezones.json");
      return Object.keys(timezones).map((key) => ({
        offset: timezones[key].offset,
        time_zone: timezones[key].time_zone,
        key: key,
        text: key + " - " + timezones[key].offset,
      }));
    },
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
          utc_time_zone: e.utc_time_zone,
          function: e.function,
          device_type: e.device_type,

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
        name: "Default Name",
        utc_time_zone: "Asia/Dubai",
        function: "auto",
        device_type: "all",
        model_number: "OX-866",
        device_id: "",
        company_id: 1,
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
