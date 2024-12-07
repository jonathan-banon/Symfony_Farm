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
        <div class="home-container flex justify-around">
            <div class="filter-container"></div>
            <div class="animals-container">
                <div v-if="!isCreated">
                    <button class="p-4 bg-primary font-semibold rounded-md focus:outline-none"
                        @click="showAddForm">Ajouter un animal</button>
                </div>
                <div v-else>
                    <form class="flex justify-between w-full" @submit.prevent="addAnimal">
                        <div>
                            <div class="animal-details">
                                <label for="name">Nom</label>
                                <input v-model="newAnimal.name" type="text" id="name" class="animal-input" />
                            </div>

                            <label for="type">Type</label>
                            <select v-model="newAnimal.type" id="type" class="animal-input"
                                @change="fetchBreeds(newAnimal.type)">
                                <option value="" disabled>Sélectionnez un type</option>
                                <option v-for="type in types" :key="type.id" :value="type.id">
                                    {{ type.name }}
                                </option>
                            </select>
                        
                            <div class="animal-details">
                                <label for="breed">Race</label>
                                <select v-model="newAnimal.breed" id="breed" class="animal-input">
                                    <option value="" disabled>Sélectionnez une race</option>
                                    <option v-for="breed in breeds" :key="breed.id" :value="breed.id">
                                        {{ breed.name }}
                                    </option>
                                </select>
                            </div>

                            <div class="animal-details">
                                <label for="age">Âge</label>
                                <input v-model="newAnimal.age" type="number" id="age" class="animal-input" />
                            </div>

                            <div class="animal-details">
                                <label for="description">Description</label>
                                <textarea v-model="newAnimal.description" id="description"
                                    class="animal-input"></textarea>
                            </div>

                            <div class="animal-details">
                                <label for="price">Prix</label>
                                <input v-model="newAnimal.price" type="number" id="price" class="animal-input" />
                            </div>
                        </div>

                        <div class="flex flex-col justify-between items-end">
                            <button type="submit" class="p-4 bg-primary font-semibold rounded-md focus:outline-none">
                                Ajouter l'animal
                            </button>
                        </div>
                    </form>
                </div>
                <div v-for="animal in animals" :key="animal.id" class="animal-item rounded-3xl h-1/2 flex">
                    <div class="animal-picture w-1/4 bg-primary rounded-3xl"></div>

                    <form class="flex justify-between w-full" v-if="isUserLoggedIn"
                        @submit.prevent="handleFormSubmit(animal)">
                        <div>
                            <div class="animal-details">
                                <label for="name">Nom</label>
                                <input v-model="animal.name" type="text" id="name" :disabled="!isUserLoggedIn"
                                    class="animal-input" />
                            </div>

                            <div class="animal-details">
                                <label for="breed">Race</label>
                                <input v-model="animal.breed" type="text" id="breed" :disabled="!isUserLoggedIn"
                                    class="animal-input" />
                            </div>

                            <div class="animal-details">
                                <label for="description">Description</label>
                                <textarea v-model="animal.description" id="description" :disabled="!isUserLoggedIn"
                                    class="animal-input"></textarea>
                            </div>

                            <div class="animal-details">
                                <label for="price">Prix</label>
                                <input v-model="animal.price" type="number" id="price" :disabled="!isUserLoggedIn"
                                    class="animal-input" />
                            </div>
                        </div>
                        <div class="flex flex-col justify-between items-end">
                            <a @click.prevent="delAnimal(animal)">
                                <img :src="trashUrl" alt="trash-logo" class="w-10">
                            </a>
                            <button type="submit"
                                class=" p-4 bg-primary  font-semibold rounded-md  focus:outline-none">Enregistrer les
                                modifications</button>
                        </div>

                    </form>


                    <div v-else class="animal-details">
                        <p><strong>Nom :</strong> {{ animal.name }}</p>
                        <p><strong>Race :</strong> {{ animal.breed }}</p>
                        <p><strong>Description :</strong> {{ animal.description }}</p>
                        <p><strong>Prix :</strong> {{ animal.price }} €</p>
                    </div>
                </div>

                <p v-if="animals.length === 0">Aucun animal trouvé pour ce type.</p>
            </div>
        </div>
    </div>
</template>

<script>
import logoUrl from '../../images/logo.svg';
import trashUrl from '../../images/trash.svg';
import LoginModal from './login/App.vue';

export default {
    components: {
        LoginModal
    },
    data() {
        return {
            types: [],
            breeds: [],
            animals: [],
            isUserLoggedIn: false,
            urlLogo: logoUrl,
            isLoginPopupVisible: false,
            trashUrl: trashUrl,
            isCreated: false,
            newAnimal: {
                type: '',
                name: '',
                breed: '',
                age: '',
                description: '',
                price: ''
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
    },
    methods: {
        async fetchBreeds(typeId) {
            console.log(typeId)
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
        showAddForm() {
            this.isCreated = true;
        },
        async addAnimal() {
            try {
                const response = await fetch('/animal/new', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify({
                        type: this.newAnimal.type,
                        name: this.newAnimal.name,
                        breed: this.newAnimal.breed,
                        age: this.newAnimal.age,
                        description: this.newAnimal.description,
                        price: this.newAnimal.price,
                    })
                });

                if (response.ok) {
                    const addedAnimal = await response.json();
                    this.animals.push(addedAnimal);
                    console.log('Animal ajouté avec succès');
                    this.isCreated = false;
                    console.log("isCreated => ", this.isCreated)
                } else {
                    console.error('Erreur lors de l\'ajout de l\'animal');
                }
            } catch (error) {
                console.error('Erreur lors de l\'envoi du formulaire:', error);
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
            try {
                const response = await fetch(`/types/animals/${typeId}`);
                const data = await response.json();
                this.animals = data.map(animal => ({
                    ...animal,
                    breed: animal.breed ? animal.breed : 'Inconnu',
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
                } else {
                    console.error('Erreur lors de la déconnexion');
                }
            } catch (error) {
                console.error('Erreur de déconnexion:', error);
            }
        },
        async handleFormSubmit(animal) {
            try {
                const response = await fetch(`/animal/${animal.id}/edit`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify({
                        name: animal.name,
                        breed: animal.breed,
                        description: animal.description,
                        price: animal.price,
                    }),
                });

                if (response.ok) {
                    console.log('Animal modifié avec succès');
                } else {
                    console.error('Erreur lors de la modification de l\'animal');
                }
            } catch (error) {
                console.error('Erreur lors de l\'envoi du formulaire:', error);
            }
        },
        async delAnimal(animal) {
            try {
                const response = await fetch(`/animal/${animal.id}/del`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify({
                        id: animal.id
                    })
                })
                if (response.ok) {
                    this.animals = this.animals.filter(a => a.id !== animal.id);
                    console.log('Animal supprimé avec succès');
                } else {
                    console.error('Erreur lors de la suppression de l\'animal');
                }
            } catch {
                console.error('Erreur lors de l\'envoi du formulaire:', error);
            }
        }
    }
}
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
    padding: 10px;
    margin: 5px 0;
    background-color: #f1f1f1;
    border-radius: 5px;
}

.animal-input {
    width: 100%;
    padding: 8px;
    margin: 5px 0;
    border-radius: 5px;
    border: 1px solid #ccc;
}

.animal-details p {
    margin: 5px 0;
}

.home-container {
    overflow: scroll;
    height: 78vh;
}
</style>
