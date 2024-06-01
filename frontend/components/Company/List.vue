<template>
  <v-autocomplete
    @change="setCompanyData"
    clearable
    label="Select Company"
    dense
    outlined
    v-model="company_id"
    :items="[{ id: ``, name: `Select All` }, ...companies]"
    item-value="id"
    item-text="name"
    :hide-details="true"
  ></v-autocomplete>
</template>
<script>
export default {
  data: () => ({
    company_id: ``,
    companies: [],
  }),
  created() {
    this.$axios.get("/amc_company_list").then(({ data }) => {
      this.companies = data;
    });
  },
  methods: {
    setCompanyData() {
      this.$emit("id", this.company_id);
    },
  },
};
</script>
