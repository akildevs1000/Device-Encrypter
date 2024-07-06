<template>
  <v-app>
    <v-container fluid>
      <v-app-bar color="teal" dark fixed app>
        <v-row>
          <v-col cols="12">
            <v-toolbar-title class="text-center white--text pa-2">
              <b>Device List Encrypter</b>
            </v-toolbar-title>
          </v-col>
        </v-row>
      </v-app-bar>

      <v-container class="mt-15">
        <v-row v-for="(item, index) in items" :key="index">
          <v-col cols="4">
            <v-text-field
              v-model="item.model_number"
              dense
              outlined
              hide-details
              label="Device Model *"
            ></v-text-field>
          </v-col>

          <v-col cols="4">
            <v-text-field
              v-model="item.device_id"
              dense
              outlined
              hide-details
              label="Device Serial *"
            ></v-text-field>
          </v-col>
        </v-row>
        <v-row>
          <v-col cols="12">
            <v-icon @click="addItem"> mdi-plus-circle-outline </v-icon>
            <v-icon v-if="items && items.length > 1" @click="removeItem">
              mdi-delete
            </v-icon>
          </v-col>
          <v-col cols="12">
            <!-- <pre>
              {{ items }}
            </pre> -->
            <table>
              <tr>
                <th>Device Model</th>
                <th>Device Serial</th>
              </tr>
              <tr
                v-for="(displayItem, displayIndex) in items"
                :key="displayIndex"
              >
                <td>{{ displayItem.model_number || "---" }}</td>
                <td>{{ displayItem.device_id || "---" }}</td>
              </tr>
            </table>
          </v-col>
          <v-col cols="12">
            <v-btn color="teal" dark @click="Encrypt">
              Encrypt
            </v-btn>

            <v-btn color="teal" dark @click="Decrypt">
              Decrypt
            </v-btn>
          </v-col>
          <v-col cols="12">
            <v-card outlined style="overflow: scroll">
              <v-card-title> Encrypted Value: </v-card-title>

              <v-card-text>
                {{ encryptedData }}
              </v-card-text>
            </v-card>
          </v-col>
          <v-col cols="12">
            <v-card outlined style="overflow: scroll">
            <v-card-title>
              Decrypted Value:
            </v-card-title>
             <v-card-text>
              {{ decryptedData }}
             </v-card-text>
            </v-card>
          </v-col>
        </v-row>
      </v-container>
    </v-container>
  </v-app>
</template>

<script>
import { encryptData, decryptData } from "../utils/encryption";

export default {
  layout: "empty",
  data() {
    return {
      dataToEncrypt: { message: "Hello, world!" },
      encryptedData: null,
      decryptedData: null,
      items: [
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
    };
  },
  methods: {
    Encrypt() {
      this.encryptedData = encryptData(this.items);

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
      this.items.push(json);
    },
    removeItem() {
      this.items.pop();
    },
  },
};
</script>
<style scoped>
@import url("@/assets/tableStyles.css");
</style>
