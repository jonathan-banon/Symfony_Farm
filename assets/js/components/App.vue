<template>
    <div class="flex justify-center items-center min-h-screen w-full h-full z-40 fixed bg-black"
        v-if="isLoginPopupVisible">
        <LoginModal :isVisible="isLoginPopupVisible" :isLoggedIn="isUserLoggedIn" @close="closeLoginPopup" />
    </div>
    <div>
        <div class="bg-secondary min-h-52 flex justify-around items-center">
            <img :src="urlLogo" alt="Logo">
            <div class="nav-container">
                <button v-for="type in types" :key="type.id" class="btn bg-primary text-white py-2 px-4 rounded-full"
                    @click="fetchAnimals(type.id)">
                    {{ type.name }}
                </button>
            </div>
            <button class="btn bg-primary text-white py-2 px-4 rounded-full" @click="toggleLoginPopup">
                {{ isUserLoggedIn ? 'Déconnexion' : 'Connexion' }}
            </button>
        </div>

        <div class="home-container flex justify-around" v-if="!isUserLoggedIn">
            <div class="filter-container"></div>
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
import LoginModal from './login/App.vue';

export default {
    components: {
        LoginModal,
    },
    data() {
        return {
            types: [],
            animals: [],
            isUserLoggedIn: false,
            urlLogo: logoUrl,
            isLoginPopupVisible: false,
        };
    },
    created() {
        this.checkSession();
    },
    mounted() {
        const appElement = document.getElementById('app');
        const typesData = appElement.getAttribute('data-types');
        this.types = JSON.parse(typesData);
    },
    methods: {
        async checkSession() {
            try {
                const response = await fetch('/check-session');
                const data = await response.json();
                console.log(data)
                this.isUserLoggedIn = data.isLoggedIn;
            } catch (error) {
                console.error('Erreur lors de la vérification de la session:', error);
                this.isUserLoggedIn = false;
            }
        },
        async fetchAnimals(typeId) {
            try {
                const response = await fetch(`/types/animals/${typeId}`);
                const data = await response.json();
                this.animals = data.map(animal => ({
                    ...animal,
                    breed: animal.breed ? animal.breed.name : 'Inconnu',
                }));
            } catch (error) {
                console.error('Erreur lors de la récupération des animaux:', error);
            }
        },
        toggleLoginPopup() {
            if (!this.isUserLoggedIn) {
                this.isLoginPopupVisible = true;
            } else {
                this.handleLogout();
            }
        },
        closeLoginPopup(isLoggedIn) {
            this.isLoginPopupVisible = false;
            this.isUserLoggedIn = isLoggedIn;
        },

        async handleLogout() {
            try {
                const response = await fetch('http://127.0.0.1:8000/disconnect', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                    },
                });

                if (response.ok) {
                    this.isUserLoggedIn = false;
                    this.$router.push('/');
                } else {
                    console.error('Erreur lors de la déconnexion');
                }
            } catch (error) {
                console.error('Erreur de déconnexion:', error);
            }
        }
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
