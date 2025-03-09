<script setup>
import axios from 'axios'
import {onMounted, ref} from 'vue'

const fixtures = ref([])
const seasonEnded = ref(false)
const teams = ref([])
const predictions = ref([])
const weeklyFixtures = ref([])

const emits = defineEmits(['resetData'])

// Group fixtures by week
const groupFixturesByWeek = (fixtureList) => {
    const grouped = {}
    fixtureList.forEach(fixture => {
        if (!grouped[fixture.week]) {
            grouped[fixture.week] = []
        }
        grouped[fixture.week].push(fixture)
    })
    return Object.entries(grouped).map(([_, weekFixtures]) => weekFixtures)
}

const fetchLeagueData = async () => {
    const [teamsResponse, predictionsResponse] = await Promise.all([
        axios.get('/api/league-teams'),
        axios.get('/api/league-predictions')
    ])

    teams.value = teamsResponse.data || []
    predictions.value = predictionsResponse.data || []
}

const playWeek = async () => {
    await axios.get('/api/play-next-week')

    const {data} = await axios.get('/api/current-week')
    weeklyFixtures.value = data || []

    if (weeklyFixtures.value.message === 'Season Completed') {
        seasonEnded.value = true
        await fetchAllFixtures()
    }

    await fetchLeagueData()
}

const fetchAllFixtures = async () => {
    const {data} = await axios.get('/api/fixtures')
    fixtures.value = groupFixturesByWeek(data || [])
}

const playSeason = async () => {
    await axios.get('/api/play-all')
    seasonEnded.value = true

    await Promise.all([
        fetchAllFixtures(),
        fetchLeagueData()
    ])
}

onMounted(async () => {
    await fetchLeagueData()

    const {data: currentWeekData} = await axios.get('/api/current-week')
    weeklyFixtures.value = currentWeekData || []

    const {data: statusData} = await axios.get('/api/season-status')
    seasonEnded.value = statusData.step === 'finished'

    if (seasonEnded.value) {
        await fetchAllFixtures()
    }
})
</script>

<template>
    <div class="row">
        <!-- CONTROLS -->
        <div class="col-md-12 d-flex justify-content-center">
            <button class="btn btn-danger" @click.prevent="emits('resetData')">Reset Data</button>
            <button class="btn btn-primary" @click.prevent="playSeason" :disabled="seasonEnded">Play Season</button>
        </div>

        <div class="col-lg-6">
            <div class="col-md-12 mb-4 mt-4">
                <div class="card">
                    <div class="card-header bg-black text-light">League Table</div>
                    <div class="card-body">
                        <table class="table table-borderless table-sm">
                            <thead>
                            <tr>
                                <th>Name</th>
                                <th>Wins</th>
                                <th>Losses</th>
                                <th>Draws</th>
                                <th>Points</th>
                                <th>Average</th>
                            </tr>
                            </thead>
                            <tbody>
                            <tr v-for="team in teams" :key="team.id">
                                <td>{{ team.name }}</td>
                                <td>{{ team.wins }}</td>
                                <td>{{ team.losses }}</td>
                                <td>{{ team.draws }}</td>
                                <td>{{ team.points }}</td>
                                <td>{{ team.average }}</td>
                            </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            <div id="season-fixtures" v-if="seasonEnded && fixtures.length > 0">
                <div class="card mb-2" v-for="(weekFixtures, index) in fixtures" :key="'week-' + index">
                    <div class="card-header bg-success text-light text-center">
                        Week {{ weekFixtures[0]?.week || index + 1 }}
                    </div>
                    <ul class="list-group list-group-flush">
                        <li
                            v-for="match in weekFixtures" :key="match.id"
                            class="list-group-item justify-content-between"
                        >
                            <div class="d-flex align-items-center justify-content-end w-100">
                                <div class="me-3">{{ match.home_team?.name || match.home_team }}</div>
                            </div>
                            <span class="badge bg-primary mx-4" v-if="match.played || match.is_played">
                {{ match.home_goals || match.home_team_goals }} - {{ match.away_goals || match.away_team_goals }}
              </span>
                            <span class="badge bg-primary mx-4" v-else>-</span>
                            <div class="d-flex w-100">
                                <div>{{ match.away_team?.name || match.away_team }}</div>
                            </div>
                        </li>
                    </ul>
                </div>
            </div>
        </div>

        <div class="col-md-3 mt-4">
            <div class="card" v-if="!seasonEnded && weeklyFixtures.length > 0">
                <div class="card-header bg-black text-light text-center">This Week</div>
                <ul class="list-group list-group-flush">
                    <li
                        v-for="match in weeklyFixtures" :key="match.id"
                        class="list-group-item d-flex align-items-center justify-content-between"
                    >
                        <div class="d-flex align-items-center justify-content-end w-100">
                            <div class="me-3">{{ match.home_team }}</div>
                        </div>
                        <span class="badge bg-primary mx-4" v-if="match.is_played">
              {{ match.home_team_goals }} - {{ match.away_team_goals }}
            </span>
                        <span class="badge bg-primary mx-4" v-else>-</span>
                        <div class="d-flex align-items-center justify-content-start w-100">
                            <div>{{ match.away_team }}</div>
                        </div>
                    </li>
                </ul>
            </div>
            <div class="col-md-12 d-flex justify-content-center mt-4">
                <button
                    class="btn btn-primary"
                    @click.prevent="playWeek"
                    :disabled="seasonEnded"
                >
                    Play Next Week
                </button>
            </div>
        </div>

        <!-- PREDICTIONS -->
        <div class="col-md-3 mt-4">
            <div class="card">
                <div class="card-header bg-black text-light text-center">Predictions</div>
                <ul class="list-group list-group-flush">
                    <li
                        v-for="team in predictions" :key="team.id"
                        class="list-group-item d-flex align-items-center justify-content-between"
                    >
                        {{ team.name }} - % {{ team.prediction }}
                    </li>
                </ul>
            </div>
        </div>
    </div>
</template>

<style scoped>
#season-fixtures {
    height: 60vh !important;
    overflow-y: scroll;
}
</style>

