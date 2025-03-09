<script setup>
import { ref, onMounted } from 'vue'
import axios from 'axios'
import Header from "./components/Header.vue";
import Home from "./components/Home.vue";
import Fixtures from "./components/Fixtures.vue";
import Simulation from "./components/Simulation.vue";

const show = ref(false)
const league = ref({
    currentStep: ''
})


const generateFixtures = async () => {
    const { data } = await axios.get('/api/start-new-league')
    league.value.currentStep = data.step
    setTimeout(() => {
        league.value.currentStep = 'fixtures'
    }, 500)

}

const startSimulation = () => {
    setTimeout(() => {
       league.value.currentStep = 'started'
    }, 500)
}

const resetData = async () => {
    const confirm = window.confirm('Are you sure you want to reset data?')
    if (!confirm) return
    const { data } = await axios.get('/api/reset-league')
    league.value.currentStep = ""
}

onMounted(async () => {
    const { data } = await axios.get('/api/status')
    league.value.currentStep = data.step
})

</script>

<template>

    <div class="container py-4">
        <Header />
        <main>
            <Home @generate-fixtures="generateFixtures" v-if="league.currentStep === ''" />
            <Fixtures @start-simulation="startSimulation" v-if="league.currentStep === 'fixtures'" />
            <Simulation @reset-data="resetData" v-if="league.currentStep === 'started' || league.currentStep === 'finished'" />
        </main>

    </div>

</template>
