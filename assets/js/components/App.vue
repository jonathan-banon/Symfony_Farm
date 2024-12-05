<template>
    <div>
        <div class="bg-secondary min-h-52 flex justify-around items-center">
            <img :src="urlLogo" alt="Logo">
            <div class="nav-container">
                <button v-for="type in types" :key="type.id" class="btn bg-primary text-white py-2 px-4 rounded-full"
                    @click="fetchAnimals(type.id)">
                    {{ type.name }}
                </button>
            </div>
            <a :href="isUserLoggedIn ? '/logout' : '/login'" class="btn bg-primary text-white py-2 px-4 rounded-full">
                {{ isUserLoggedIn ? 'Déconnexion' : 'Connexion' }}
            </a>
        </div>
        <div class="home-container flex justify-around">
            <div class="filter-container">

            </div>
            <div class="animals-container">
                <div v-for="animal in animals" :key="animal.id" class="animal-item rounded-3xl h-1/3 flex">
                    <div class="animal-picture w-1/4 bg-primary rounded-3xl"></div>
                    <div class="animal-content">
                        <p>{{ animal.name }}</p>
                        <p>{{ animal.breed }}</p>
                        <p>{{ animal.description }}</p>
                    </div>
                </div>
                <p v-if="animals.length === 0">Aucun animal trouvé pour ce type.</p>
            </div>
        </div>
    </div>
</template>

<script>
import logoUrl from '../../images/logo.svg';
export default {
    data() {
        return {
            types: [],
            animals: [],
            isUserLoggedIn: false,
            urlLogo: logoUrl
        };
    },
    mounted() {
        const appElement = document.getElementById('app');
        const typesData = appElement.getAttribute('data-types');
        const userLoggedIn = appElement.getAttribute('data-user-logged-in');

        this.types = JSON.parse(typesData);
        this.isUserLoggedIn = userLoggedIn === 'true';
    },
    methods: {
        async fetchAnimals(typeId) {
            try {
                const response = await fetch(`/types/animals/${typeId}`);
                const data = await response.json();
                this.animals = data;
                console.log(this.animals)
            } catch (error) {
                console.error('Erreur lors de la récupération des animaux:', error);
            }
        },
    },
};
</script>

<style scoped>
.filter-container {
    width: 28vw;
    height: 70vh;
}
.animals-container {
    width: 70vw;
}

.btn {
    font-size: 1.5rem;
    padding: 15px;
    border-radius: 20px;
    width: fit-content;
    height: fit-content;
}

.nav-container {
    display: flex;
    min-width: 50%;
    justify-content: space-around;
    align-items: end;
}

.animal-item {
    padding: 5px;
    margin: 5px 0;
    background-color: #f1f1f1;
    border-radius: 5px;
}
</style>