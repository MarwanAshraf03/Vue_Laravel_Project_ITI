<script setup>
const emit = defineEmits(['update:filters'])
const filters = defineModel('filters', { default: () => ({
  search: '',
  category: '',
  location: '',
  industry: '',
  experience: '',
  posted: '',
  min_salary: '',
  max_salary: ''
})})

function updateFilter(key, value) {
  filters.value[key] = value
  emit('update:filters', filters.value)
}
</script>

<template>
  <aside class="filters-sidebar">
    <h3>Filters</h3>

    <div class="filter-group">
      <label>Search</label>
      <input type="text" v-model="filters.search" @input="updateFilter('search', filters.search)" placeholder="Search jobs..." />
    </div>

    <div class="filter-group">
      <label>Category</label>
      <select v-model="filters.category" @change="updateFilter('category', filters.category)">
        <option value="">All</option>
        <option value="it">IT</option>
        <option value="design">Design</option>
        <option value="marketing">Marketing</option>
        <option value="data">Data</option>
        <option value="management">Management</option>
      </select>
    </div>

    <div class="filter-group">
      <label>Location</label>
      <select v-model="filters.location" @change="updateFilter('location', filters.location)">
        <option value="">All</option>
        <option value="cairo">Cairo</option>
        <option value="giza">Giza</option>
        <option value="alexandria">Alexandria</option>
      </select>
    </div>

    <div class="filter-group">
      <label>Industry</label>
      <select v-model="filters.industry" @change="updateFilter('industry', filters.industry)">
        <option value="">All</option>
        <option value="software">Software</option>
        <option value="creative">Creative</option>
        <option value="retail">Retail</option>
        <option value="fintech">Fintech</option>
        <option value="finance">Finance</option>
        <option value="tech">Tech</option>
      </select>
    </div>

    <div class="filter-group">
      <label>Experience Level</label>
      <select v-model="filters.experience" @change="updateFilter('experience', filters.experience)">
        <option value="">All</option>
        <option value="junior">Junior</option>
        <option value="mid">Mid</option>
        <option value="senior">Senior</option>
      </select>
    </div>

    <div class="filter-group">
      <label>Posted</label>
      <select v-model="filters.posted" @change="updateFilter('posted', filters.posted)">
        <option value="">Any time</option>
        <option value="24h">Last 24 hours</option>
        <option value="7days">Last 7 days</option>
      </select>
    </div>

    <div class="filter-group">
      <label>Min Salary</label>
      <input type="number" v-model="filters.min_salary" @input="updateFilter('min_salary', filters.min_salary)" placeholder="Min" />
    </div>

    <div class="filter-group">
      <label>Max Salary</label>
      <input type="number" v-model="filters.max_salary" @input="updateFilter('max_salary', filters.max_salary)" placeholder="Max" />
    </div>
  </aside>
</template>

<style scoped>
.filters-sidebar {
  background: white;
  border: 1px solid rgba(11, 28, 48, 0.1);
  border-radius: 16px;
  padding: 1.5rem;
  height: fit-content;
  position: sticky;
  top: 1rem;
}

.filters-sidebar h3 {
  margin-top: 0;
  color: #0b1c30;
}

.filter-group {
  margin-bottom: 1rem;
}

.filter-group label {
  display: block;
  margin-bottom: 0.35rem;
  color: #5f6b7c;
  font-size: 0.9rem;
  font-weight: 600;
}

.filter-group input,
.filter-group select {
  width: 100%;
  padding: 0.65rem;
  border: 1px solid rgba(11, 28, 48, 0.15);
  border-radius: 8px;
  font-size: 0.95rem;
}
</style>
