<script setup>
import axios from 'axios'
import { ref, onMounted } from 'vue'

const fixtures = ref([])

const emits = defineEmits(['startSimulation'])

onMounted(async () => {
    const { data } = await axios.get('/api/fixtures');
    fixtures.value = data ?? []
});

// Function to group fixtures by week
const groupFixturesByWeek = () => {
    const groupedFixtures = {};
    fixtures.value.forEach(fixture => {
        if (!groupedFixtures[fixture.week]) {
            groupedFixtures[fixture.week] = [];
        }
        groupedFixtures[fixture.week].push(fixture);
    });
    return groupedFixtures;
}
</script>

<template>
    <div class="w-100 text-center pt-2 pb-1">
        <h1 class="fs-4 mb-0 pb-0">Fixtures</h1>
    </div>
    <hr class="w-25 mx-auto">
    <div class="row">
        <template v-for="(fixturesInWeek, week) in groupFixturesByWeek()">
            <div class="col-lg-4 mb-4">
                <div class="card">
                    <div class="card-header bg-black text-light">Week {{ week }}</div>
                    <ul class="list-group list-group-flush">
                        <li v-for="(fixture, index) in fixturesInWeek" :key="fixture.id"
                            class="list-group-item d-flex align-items-center justify-content-between">
                            <div class="d-flex w-100">
                                <div class="me-3">{{fixture.home_team}}</div>
                            </div>
                            <span class="mx-4">-</span>
                            <div class="d-flex w-100">
                                <div>{{ fixture.away_team }}</div>
                            </div>
                        </li>
                    </ul>
                </div>
            </div>
        </template>
    </div>
    <template v-if="fixtures.length">
        <hr class="w-25 mx-auto mb-4">
        <div class="d-flex justify-content-center">
            <button class="btn btn-success btn-lg" type="button" @click.prevent="emits('startSimulation')">
                Start Simulation
            </button>
        </div>
    </template>
</template>
