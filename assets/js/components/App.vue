<template>
    <template v-if="isDesktop">
        <div v-if="isPopupVisible"
            class="flex justify-center items-center min-h-screen w-full h-full z-30 fixed bg-black">
            <LoginForm :popup="popup" @close="closeLoginForm" />
            <template v-if="showAddForm">
                <AddAnimalForm :types="types" :breeds="breeds" :actualTypeId="actualTypeId"
                    v-model:showAddForm="showAddForm" v-model:isPopupVisible="isPopupVisible" @close="toggleAddForm"
                    @fetchBreeds="fetchBreeds" @fetchAnimals="fetchAnimals" @addBreed="addBreed" />
            </template>
            <template v-if="showTypeForm">
                <AddTypeForm :actualTypeId="actualTypeId" v-model:showTypeForm="showTypeForm"
                    v-model:isPopupVisible="isPopupVisible" @close="toggleTypeForm" @fetchTypes="fetchTypes" />
            </template>
            <template v-if="showBreedForm">
                <BreedForm :actualTypeId="actualTypeId" :types="types" v-model:breeds="breeds" :trashUrl="trashUrl"
                    :penUrl="penUrl" v-model:showBreedForm="showBreedForm" v-model:isPopupVisible="isPopupVisible"
                    @toggle-breed-form="toggleBreedForm" @fetchBreeds="fetchBreeds" @addBreed="addBreed" />
            </template>
        </div>
        <Navbar v-model:types="types" v-model:actualTypeId="actualTypeId" :urlLogo="urlLogo" :addLogo="addLogo"
            :textLogoUrl="textLogoUrl" :trashUrl="trashUrl" :penUrl="penUrl" :isUserLoggedIn="isUserLoggedIn"
            @toggleLogin="toggleLoginForm" @fetchAnimals="fetchAnimals" @toggleTypeForm="toggleTypeForm" />

        <template v-if="isUserLoggedIn">
            <AdminAnimals v-model:animals="animals" :breeds="breeds" :actualTypeId="actualTypeId" :trashUrl="trashUrl"
                @prev-image="prevImage" @next-image="nextImage" @fetchAnimals='fetchAnimals'
                @toggleBreedForm="toggleBreedForm" @toggleAddAnimalForm="toggleAddAnimalForm" />
        </template>
        <template v-else>
            <UserAnimals @toggleFilter="toggleFilter" :animals="animals" :breeds="breeds" />
        </template>
    </template>
    <template v-else>
        <div id="mobile-wip">
            <div class="mobile-popup">
                <span>version mobile :<br>work in progress</span>
            </div>
        </div>
    </template>
</template>

<script>
import addLogo from '../../images/add.svg';
import logoUrl from '../../images/logo.svg';
import textLogoUrl from '../../images/family-farm.svg';
import trashUrl from '../../images/trash.svg';
import penUrl from '../../images/pen.svg';
import Navbar from './Navbar.vue';
import AddAnimalForm from './AddAnimalForm.vue';
import AddTypeForm from './AddTypeForm.vue';
import BreedForm from './BreedForm.vue';
import AdminAnimals from './AdminAnimals.vue';
import UserAnimals from './UserAnimals.vue';
import LoginForm from './LoginForm.vue';

export default {
    components: {
        Navbar,
        AddAnimalForm,
        AddTypeForm,
        BreedForm,
        AdminAnimals,
        UserAnimals,
        LoginForm
    },
    data() {
        return {
            isDesktop: window.innerWidth > 1024,
            actualTypeId: 1,
            types: [],
            breeds: [],
            animals: [],
            isUserLoggedIn: false,
            urlLogo: logoUrl,
            textLogoUrl: textLogoUrl,
            isPopupVisible: false,
            popup: '',
            trashUrl: trashUrl,
            addLogo: addLogo,
            penUrl: penUrl,
            showAddForm: false,
            showTypeForm: false,
            showBreedForm: false,
            isTypeSelected: false,
            editingTypeId: null,
            editingBreedId: null,
            newBreed: {
                name: '',
                typeId: 1,
            }
        };
    },
    created() {
        this.checkSession();
    },
    mounted() {
        const appElement = document.getElementById('app');
        const typesData = appElement.getAttribute('data-types');
        this.types = JSON.parse(typesData);
        const savedTypeId = localStorage.getItem('actualTypeId');
        this.actualTypeId = savedTypeId ? parseInt(savedTypeId, 10) : 1;
        this.fetchAnimals(this.actualTypeId);
    },
    methods: {
        async fetchTypes() {
            try {
                const response = await fetch(`/type/all`);
                if (response.ok) {
                    this.types = await response.json();
                }
            } catch (error) {
                console.error('Error fetching types:', error);
            }
        },
        async fetchBreeds(typeId) {
            this.isTypeSelected = true;
            try {
                const response = await fetch(`/type/${typeId}/breeds`);
                if (response.ok) {
                    this.breeds = await response.json();
                } else {
                    console.error('Erreur lors de la récupération des races');
                }
            } catch (error) {
                console.error('Erreur lors de la récupération des races:', error);
            }
        },
        async checkSession() {
            try {
                const response = await fetch('/check-session');
                const data = await response.json();
                this.isUserLoggedIn = data.isLoggedIn;
            } catch (error) {
                console.error('Erreur lors de la vérification de la session:', error);
                this.isUserLoggedIn = false;
            }
        },
        async fetchAnimals(typeId) {
            this.actualTypeId = typeId;
            this.fetchBreeds(this.actualTypeId);
            localStorage.setItem('actualTypeId', typeId);
            try {
                const response = await fetch(`/type/${typeId}/animals`);
                const data = await response.json();
                this.animals = data.map(animal => ({
                    ...animal,
                    breed: animal.breed ? animal.breed : 'Inconnu',
                }));
            } catch (error) {
                console.error('Erreur lors de la récupération des animaux:', error);
            }
        },
        toggleFilter() {
            this.isPopupVisible = !this.isPopupVisible;
        },
        toggleLoginForm() {
            if (!this.isUserLoggedIn) {
                this.isPopupVisible = true;
                this.popup = "login";
            } else {
                this.handleLogout();
            }
        },
        toggleAddForm() {
            this.showAddForm = !this.showAddForm;
            this.isPopupVisible = !this.isPopupVisible;
        },
        toggleTypeForm() {
            this.showTypeForm = !this.showTypeForm;
            this.isPopupVisible = !this.isPopupVisible;
        },
        toggleBreedForm() {
            this.showBreedForm = !this.showBreedForm;
            this.isPopupVisible = !this.isPopupVisible;
        },
        toggleAddAnimalForm() {
            this.showAddForm = !this.showAddForm;
            this.isPopupVisible = !this.isPopupVisible;
        },
        closeLoginForm(isLoggedIn) {
            this.isPopupVisible = false;
            this.popup = '';
            this.isUserLoggedIn = isLoggedIn;
            this.fetchAnimals(this.actualTypeId);
        },
        async handleLogout() {
            try {
                const response = await fetch('/disconnect', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                    },
                });

                if (response.ok) {
                    this.isUserLoggedIn = false;
                    this.fetchAnimals(this.actualTypeId);
                } else {
                    console.error('Erreur lors de la déconnexion');
                }
            } catch (error) {
                console.error('Erreur de déconnexion:', error);
            }
        },
        prevImage(animal) {
            if (animal.currentImageIndex > 0) {
                animal.currentImageIndex--;
            } else {
                animal.currentImageIndex = animal.images.length - 1;
            }
        },
        nextImage(animal) {
            if (animal.currentImageIndex < animal.images.length - 1) {
                animal.currentImageIndex++;
            } else {
                animal.currentImageIndex = 0;
            }
        },
        async addBreed(typeId, breedName, resolve, reject) {
            try {
                const response = await fetch(`/breed/${typeId}/add`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify({
                        name: breedName,
                    })
                });
                if (response.ok) {
                    const newBreed = await response.json();
                    await this.fetchBreeds(typeId);
                    resolve(newBreed);
                } else {
                    console.error('Error response from server when adding breed');
                    reject(new Error('Erreur lors de l\'ajout de la race'));
                }
            } catch (error) {
                console.error('Exception in addBreed:', error);
                reject(error);
            }
        }
    }
}
</script>

<style>
@import '../../../assets/styles/style.css';


#mobile-wip {
    display: block;
    position: absolute;
    top: 0;
    bottom: 0;
    left: 0;
    right: 0;
    background-color: rgba(0, 0, 0, 0.3);
    backdrop-filter: blur(8px);
    width: 100vw;
    height: 100vh;
}

.mobile-popup {
    width: 300px;
    height: 200px;
    background-color: white;
    color: rgba(88, 45, 9);
    display: flex;
    text-align: center;
    align-items: center;
    margin: auto;
    margin-top: 20vh;
    border-radius: 10px;
    filter: drop-shadow(0 2px 4px rgba(40, 23, 9, 0.25));
}

.mobile-popup span {
    margin: auto;
    font-size: 1.5rem;
    font-weight: bold;
    line-height: 2rem;
    text-transform: uppercase;
}
</style>