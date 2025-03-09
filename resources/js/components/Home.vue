<script setup>
import { ref, onMounted } from 'vue'
import axios from 'axios'

const emits = defineEmits(['toggleLoader'])
const teams = ref([])

onMounted(async () => {
    const { data } = await axios.get('/api/league-teams')
    teams.value = data
})
</script>

<template>
    <div class="row">
        <div class="col-md-9">
            <div class="p-5 mb-4 bg-light rounded-3">
                <button class="btn btn-primary btn-lg" type="button"
                        @click="emits('generateFixtures')">
                    Generate Fixtures
                </button>
            </div>
        </div>
        <div class="col-lg-3">
            <div class="card">
                <div class="card-header bg-prem text-light">League Teams</div>
                <ul class="list-group list-group-flush">
                    <li
                        v-for="team in teams" :key="team.name"
                        class="list-group-item d-flex align-items-center justify-content-between px-3 py-2">
                        <div>
                            <span>{{ team.name }}</span>
                        </div>
                    </li>
                </ul>
            </div>
        </div>
    </div>
</template>

<style scoped>

</style>
