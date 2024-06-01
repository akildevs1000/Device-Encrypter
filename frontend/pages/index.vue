<template>
  <span>
    <v-data-table
      dense
      :headers="headers"
      :items="companies"
      :loading="loading"
      :options.sync="options"
      :footer-props="{
        itemsPerPageOptions: [100, 500, 1000],
      }"
      class="elevation-1 pa-3"
    >
      <template v-slot:top>
        <v-toolbar flat dense class="mb-5">
          Companies
          <v-icon color="primary" right class="mt-1" @click="getDataFromApi()"
            >mdi-reload</v-icon
          >
          <v-spacer></v-spacer>
          <CompanyCreate @response="getDataFromApi" />
        </v-toolbar>
      </template>
      <template v-slot:item.options="{ item }">
        <v-menu bottom left>
          <template v-slot:activator="{ on, attrs }">
            <div class="text-center">
              <v-btn dark-2 icon v-bind="attrs" v-on="on">
                <v-icon>mdi-dots-vertical</v-icon>
              </v-btn>
            </div>
          </template>
          <v-list width="120" dense>
            <v-list-item>
              <v-list-item-title>
                <CompanyEdit :item="item" @response="getDataFromApi" />
              </v-list-item-title>
            </v-list-item>
            <v-list-item>
              <v-list-item-title>
                <Device :item="item" @response="getDataFromApi" />
              </v-list-item-title>
            </v-list-item>
            <v-list-item>
              <v-list-item-title>
                <Delete
                  :id="item.id"
                  endpoint="company"
                  @response="getDataFromApi"
                />
              </v-list-item-title>
            </v-list-item>
          </v-list>
        </v-menu>
      </template>
    </v-data-table>
  </span>
</template>

<script>
let date = new Date();

let d = date.getDate();
let m = (date.getMonth() + 1).toString().padStart(2, "0");
let y = date.getFullYear();
let currentDate = y + "-" + m + "-" + d;

export default {
  data: () => ({
    currentDate,
    filters: {},
    options: {},
    loading: false,
    response: "",
    companies: [
      {
        name: "Akil Security",
        number: "0554501483",
        email: "akil@gmail.com",
        location: "Dubai",
      },
      {
        name: "SecureTech",
        number: "0501234567",
        email: "info@securetech.com",
        location: "Abu Dhabi",
      },
      {
        name: "SafeGuard Systems",
        number: "0522345678",
        email: "contact@safeguardsystems.ae",
        location: "Sharjah",
      },
      {
        name: "Guardian Security",
        number: "0569876543",
        email: "support@guardiansecurity.ae",
        location: "Ajman",
      },
      {
        name: "Defense Solutions",
        number: "0541239876",
        email: "sales@defensesolutions.ae",
        location: "Ras Al Khaimah",
      },
      {
        name: "Watchful Eye",
        number: "0534567890",
        email: "admin@watchfuleye.ae",
        location: "Fujairah",
      },
    ],
    errors: [],
    headers: [
      {
        text: "Ref #",
        align: "left",
        sortable: true,
        key: "id",
        value: "id",
        filterable: true,
        filterSpecial: false,
      },
      {
        text: "Name",
        align: "left",
        sortable: true,
        key: "name",
        value: "name",
        filterable: true,
        filterSpecial: false,
      },
      {
        text: "Number",
        align: "left",
        sortable: true,
        key: "number",
        value: "number",
        filterable: true,
        filterSpecial: false,
      },
      {
        text: "Email",
        align: "left",
        sortable: true,
        key: "email",
        value: "email",
        filterable: true,
        filterSpecial: false,
      },
      {
        text: "Location",
        align: "left",
        sortable: true,
        key: "location",
        value: "location",
        filterable: true,
        filterSpecial: false,
      },

      {
        text: "Action",
        align: "center",
        sortable: false,
        key: "options",
        value: "options",
      },
    ],
  }),

  async created() {
    this.getDataFromApi();
  },
  mounted() {},
  watch: {
    options: {
      handler() {
        this.getDataFromApi();
      },
      deep: true,
    },
  },
  methods: {
    getRandomId() {
      return Math.floor(Math.random() * 1000000 + 1);
    },
    async getDataFromApi() {
      this.loading = true;
      let { data } = await this.$axios.get("company");
      this.loading = false;

      this.companies = data.data;
    },
  },
};
</script>
