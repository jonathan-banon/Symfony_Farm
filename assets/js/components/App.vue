<template>
    <div class="flex justify-center items-center min-h-screen w-full h-full z-40 fixed bg-black"
        v-if="isLoginPopupVisible">
        <LoginModal :isVisible="isLoginPopupVisible" :isLoggedIn="isUserLoggedIn" @close="closeLoginPopup" />
    </div>
    <div class="bg-secondary min-h-60 flex justify-around items-center">
        <p class="alert-success bg-primary" v-if="isAlertVisible">{{ alertMessage }}</p>
        <img :src="urlLogo" alt="Logo">
        <div class="nav-container">
            <div v-for="type in types" :key="type.id" class="type-container flex">
                <button class="btn bg-primary text-white py-2 px-4 rounded-full" @click="fetchAnimals(type.id)">
                    {{ type.name }}
                </button>
                <a @click.prevent="delType(type.id)" class="delete-type-icon">
                    <img :src="trashUrl" alt="trash-logo" class="w-6">
                </a>
            </div>
        </div>
        <button class="btn bg-primary text-white py-2 px-4 rounded-full" @click="toggleLoginPopup">
            {{ isUserLoggedIn ? 'Déconnexion' : 'Connexion' }}
        </button>
    </div>
    <template v-if="isUserLoggedIn">
        <div class="home-container flex justify-around">
            <div class="filter-container">
                <div v-if="!showAddForm && !showTypeForm && !showBreedForm" class="flex justify-around">
                    <button class="p-4 bg-primary font-semibold rounded-md focus:outline-none"
                        @click="toggleAddForm">Ajouter un animal</button>
                    <button class="p-4 bg-primary font-semibold rounded-md focus:outline-none"
                        @click="toggleTypeForm">Ajouter un Type d'animal</button>
                    <button class="p-4 bg-primary font-semibold rounded-md focus:outline-none"
                        @click="toggleBreedForm">Ajouter une race d'animal</button>
                </div>
            </div>
            <div class="animals-container">
                <div v-if="showAddForm">
                    <form class="flex justify-between w-full" @submit.prevent="addAnimal">
                        <div>
                            <div class="animal-details">
                                <label for="name">Nom</label>
                                <input required v-model="newAnimal.name" type="text" id="name" class="animal-input" />
                            </div>

                            <label for="type">Type</label>
                            <select required v-model="newAnimal.type" id="type" class="animal-input"
                                @change="fetchBreeds(newAnimal.type)">
                                <option value="" disabled>Sélectionnez un type</option>
                                <option v-for="type in types" :key="type.id" :value="type.id">
                                    {{ type.name }}
                                </option>
                            </select>

                            <div class="animal-details" v-if="isTypeSelected">
                                <label for="breed">Race</label>
                                <select required v-model="newAnimal.breed" id="breed" class="animal-input">
                                    <option value="" disabled>Sélectionnez une race</option>
                                    <option v-for="breed in breeds" :key="breed.id" :value="breed.id">
                                        {{ breed.name }}
                                    </option>
                                </select>
                            </div>

                            <div class="animal-details">
                                <label for="age">Âge</label>
                                <input required v-model="newAnimal.age" type="number" min="1" id="age"
                                    class="animal-input" />
                            </div>

                            <div class="animal-details">
                                <label for="description">Description</label>
                                <textarea required v-model="newAnimal.description" id="description"
                                    class="animal-input"></textarea>
                            </div>

                            <div class="animal-details">
                                <label for="price">Prix</label>
                                <input required v-model="newAnimal.price" type="number" min="0" id="price"
                                    class="animal-input" />
                            </div>
                        </div>

                        <div class="flex flex-col justify-between items-end">
                            <button class="p-4 bg-primary font-semibold rounded-md focus:outline-none"
                                @click="toggleAddForm">Retour</button>
                            <button type="submit" class="p-4 bg-primary font-semibold rounded-md focus:outline-none">
                                Ajouter l'animal
                            </button>
                        </div>
                    </form>
                </div>
                <div v-if="showTypeForm">
                    <form class="flex justify-between w-full" @submit.prevent="addType">
                        <div>
                            <div class="animal-details">
                                <label for="name">Nom</label>
                                <input required v-model="newType.name" type="text" id="name" class="animal-input" />
                            </div>
                            <button class="p-4 bg-primary font-semibold rounded-md focus:outline-none"
                                @click="toggleTypeForm">Retour</button>
                            <button type="submit" class="p-4 bg-primary font-semibold rounded-md focus:outline-none">
                                Ajouter le type d'animal
                            </button>
                        </div>
                    </form>
                </div>
                <div v-if="showBreedForm">
                    <form class="flex justify-between w-full" @submit.prevent="addBreed(newBreed.typeId)">
                        <div>
                            <label for="type">Type</label>
                            <select required v-model="newBreed.typeId" id="type" class="animal-input">
                                <option value="" disabled>Sélectionnez un type</option>
                                <option v-for="type in types" :key="type.id" :value="type.id">
                                    {{ type.name }}
                                </option>
                            </select>

                            <div class="animal-details">
                                <label for="name">Nom</label>
                                <input required v-model="newBreed.name" type="text" id="name" class="animal-input" />
                            </div>
                            <button class="p-4 bg-primary font-semibold rounded-md focus:outline-none"
                                @click="toggleBreedForm">Retour</button>
                            <button type="submit" class="p-4 bg-primary font-semibold rounded-md focus:outline-none">
                                Ajouter une race d'animal
                            </button>
                        </div>
                    </form>
                </div>
                <div v-for="animal in animals" :key="animal.id" class="animal-item rounded-3xl h-1/2 flex">
                    <form class="flex justify-between w-full" @submit.prevent="editAnimal(animal)"
                        enctype="multipart/form-data">
                        <div class="animal-picture w-1/4 bg-primary rounded-3xl" :style="{
                            backgroundImage: 'url(' + animal.images[animal.currentImageIndex] + ')',
                            backgroundSize: 'cover',
                            backgroundPosition: 'center'
                        }">
                            <a @click.prevent="delImage(animal)">
                                <img :src="trashUrl" alt="trash-logo" class="w-10">
                            </a>
                            <div class="carousel-container" v-if="animal.images.length > 1">
                                <img class="carousel-btn left" @click="prevImage(animal)">
                                </img>
                                <img class="carousel-btn right" @click="nextImage(animal)">
                                </img>
                            </div>

                            <div class="overlay bg-opacity-50 p-2">
                                <input type="file" @change="onFileChange($event, animal)">
                                <button @click="uploadImage(animal)">Télécharger</button>
                            </div>
                        </div>
                        <div>
                            <div class="animal-details">
                                <label for="name">Nom</label>
                                <input v-model="animal.name" type="text" id="name" class="animal-input" />
                            </div>

                            <label for="breed">Race</label>
                            <select v-model="animal.breed" id="breed" class="animal-input">
                                <option :value="animal.breed" disabled>
                                    {{ animal.breed }}
                                </option>
                                <option v-for="breed in breeds" :key="breed.id" :value="breed.id">
                                    {{ breed.name }}
                                </option>
                            </select>

                            <div class="animal-details">
                                <label for="description">Description</label>
                                <textarea v-model="animal.description" id="description" class="animal-input"></textarea>
                            </div>

                            <div class="animal-details">
                                <label for="price">Prix</label>
                                <input v-model="animal.price" type="number" id="price" class="animal-input" />
                            </div>
                        </div>
                        <div class="animal-details">
                            <label for="isOnSale">Status</label>
                            <div>
                                <div>
                                    <input type="radio" v-model="animal.isOnSale" :value="true" />
                                    <span class="ml-2">En vente</span>
                                </div>
                                <div>
                                    <input type="radio" v-model="animal.isOnSale" :value="false" />
                                    <span class="ml-2">Vendu</span>
                                </div>
                            </div>
                        </div>
                        <div class="flex flex-col justify-between items-end">
                            <a @click.prevent="delAnimal(animal)">
                                <img :src="trashUrl" alt="trash-logo" class="w-10">
                            </a>
                            <button type="submit"
                                class=" p-4 bg-primary  font-semibold rounded-md  focus:outline-none">Enregistrer
                                les
                                modifications</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </template>
    <template v-else>
        <div class="home-container flex justify-around">
            <div class="filter-container"></div>
            <div class="animals-container">
                <div v-for="animal in animals" :key="animal.id" class="animal-item rounded-3xl h-1/2 flex">
                    <div class="animal-picture w-1/4 bg-primary rounded-3xl" :style="{
                        backgroundImage: 'url(' + animal.images[animal.currentImageIndex] + ')',
                        backgroundSize: 'cover',
                        backgroundPosition: 'center'
                    }">
                        <div class="carousel-container" v-if="animal.images.length > 1">
                            <img class="carousel-btn left" @click="prevImage(animal)">
                            </img>
                            <img class="carousel-btn right" @click="nextImage(animal)">
                            </img>
                        </div>
                    </div>
                    <div class="animal-details">
                        <p><strong>Nom :</strong> {{ animal.name }}</p>
                        <p><strong>Race :</strong> {{ animal.breed }}</p>
                        <p><strong>Description :</strong> {{ animal.description }}</p>
                        <p><strong>Prix :</strong> {{ animal.price }} €</p>
                    </div>
                </div>
            </div>
        </div>
    </template>
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
            actualTypeId: 1,
            types: [],
            breeds: [],
            animals: [],
            isUserLoggedIn: false,
            urlLogo: logoUrl,
            isLoginPopupVisible: false,
            isAlertVisible: false,
            trashUrl: trashUrl,
            showAddForm: false,
            showTypeForm: false,
            showBreedForm: false,
            isTypeSelected: false,
            alertMessage: "",
            newAnimal: {
                type: '',
                name: '',
                breed: '',
                age: '',
                description: '',
                price: '',
            },
            newType: {
                name: '',
            },
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
        onFileChange(e, animal) {
            if (e.target.files[0]) {
                animal.file = e.target.files[0];
                const imageUrl = URL.createObjectURL(e.target.files[0]);
                animal.images.push(imageUrl);
                animal.currentImageIndex = animal.images.length - 1;
            }
        },
        async uploadImage(animal) {
            if (!animal.file) {
                alert("Veuillez sélectionner un fichier.");
                return;
            }

            const formData = new FormData();
            formData.append("image", animal.file);

            try {
                const response = await fetch(`/animal/${animal.id}/upload-image`, {
                    method: "POST",
                    body: formData,
                });
                if (response.ok) {
                    const data = await response.json();
                    alert("Image téléchargée avec succès !");
                    animal.previewImage = data.imagePath;
                } else {
                    //throw new Error("Échec de l'upload de l'image.");
                }

            } catch (error) {
                console.error("Erreur :", error);
                alert(error.message);
            }
        },
        async fetchTypes() {
            try {
                const response = await fetch(`/type/all`);
                if (response.ok) {
                    this.types = await response.json();
                }
            } catch (error) {

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
                    this.fetchAnimals(this.actualTypeId)
                    this.showAddForm = false;
                    this.isAlertVisible = true;
                    this.alertMessage = "Animal ajouté avec succès";
                    setTimeout(() => {
                        this.isAlertVisible = false;
                    }, 3000)
                } else {
                    console.error('Erreur lors de l\'ajout de l\'animal');
                }
            } catch (error) {
                console.error('Erreur lors de l\'envoi du formulaire:', error);
            }
        },
        async addType() {
            try {
                const response = await fetch('/type/new', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify({
                        name: this.newType.name,
                    })
                });

                if (response.ok) {
                    this.fetchAnimals(this.actualTypeId)
                    this.fetchTypes();
                    this.showTypeForm = false;
                    this.isAlertVisible = true;
                    this.alertMessage = "Type d'animal ajouté avec succès"
                    setTimeout(() => {
                        this.isAlertVisible = false
                    }, 3000)
                } else {
                    console.error('Erreur lors de l\'ajout du type');
                }
            } catch (error) {
                console.error('Erreur lors de l\'envoi du formulaire:', error);
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
        async addBreed(typeId) {
            try {
                const response = await fetch((`/breed/${typeId}/add`), {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify({
                        name: this.newBreed.name,
                    })
                });

                if (response.ok) {
                    this.fetchAnimals(this.actualTypeId)
                    this.showBreedForm = false;
                    this.isAlertVisible = true;
                    this.alertMessage = "Race d\'animal ajoutée avec succès"
                    setTimeout(() => {
                        this.isAlertVisible = false;
                    }, 3000)
                } else {
                    console.error('Erreur lors de l\'ajout');
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
        toggleLoginPopup() {
            if (!this.isUserLoggedIn) {
                this.isLoginPopupVisible = true;
            } else {
                this.handleLogout();
            }
        },
        toggleAddForm() {
            this.showAddForm = !this.showAddForm
        },
        toggleTypeForm() {
            this.showTypeForm = !this.showTypeForm
        },
        toggleBreedForm() {
            this.showBreedForm = !this.showBreedForm
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
        async editAnimal(animal) {
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
                        status: animal.isOnSale

                    }),
                });

                if (response.ok) {
                    this.isAlertVisible = true
                    this.alertMessage = "Animal modifié avec succès"
                    setTimeout(() => {
                        this.isAlertVisible = false
                    }, 3000)

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
                    this.isAlertVisible = true;
                    this.alertMessage = "Annonce de l'animal supprimé avec succès"
                    this.animals = this.animals.filter(a => a.id !== animal.id);
                    setTimeout(() => {
                        this.isAlertVisible = false;
                    }, 3000)

                } else {
                    console.error('Erreur lors de la suppression de l\'animal');
                }
            } catch {
                console.error('Erreur lors de l\'envoi du formulaire:', error);
            }
        },
        async delType(id) {
            try {
                const response = await fetch(`/type/${id}/del`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                    }
                })
                if (response.ok) {
                    this.isAlertVisible = true;
                    this.alertMessage = "Type d'animal supprimé avec succès";
                    const data = await response.json();
                    if (data.newTypeId) {
                        this.actualTypeId = data.newTypeId;
                        this.fetchAnimals(this.actualTypeId);
                    }
                    this.types = this.types.filter(type => type.id !== id);
                    setTimeout(() => {
                        this.isAlertVisible = false;
                    }, 3000)

                } else {
                    console.error('Erreur lors de la suppression du type d\'animal');
                }
            } catch {
                console.error('Erreur lors de l\'envoi du formulaire:', error);
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
        async delImage(animal) {
            try {
                const response = await fetch(`/animal/${animal.id}/delete-image/${animal.currentImageIndex}`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify({
                        id: animal.id,
                        currentImageIndex: animal.currentImageIndex
                    })

                })
                if (response.ok) {
                    this.fetchAnimals(this.actualTypeId)
                    this.isAlertVisible = true;
                    this.alertMessage = "Image de l'animal supprimé avec succès"
                }
            } catch {

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

.alert-success {
    position: fixed;
    top: 15px;
    right: 15px;
    width: 20%;
    border-radius: 10px;
    padding: 10px;
    text-align: center;
    font-weight: bold;
}

.animal-container {
    display: flex;
    margin-bottom: 20px;
}

.animal-picture {
    position: relative;
    height: 300px;
    overflow: hidden;
}

.carousel-container {
    position: relative;
    width: 100%;
    height: 100%;
}

.carousel-image {
    width: 100%;
    height: 100%;
    background-size: cover;
    background-position: center;
    border-radius: 15px;
    transition: opacity 0.5s ease-in-out;
}

.carousel-btn {
    position: absolute;
    top: 50%;
    transform: translateY(-50%);
    background-color: rgba(0, 0, 0, 0.5);
    color: white;
    border: none;
    padding: 10px;
    cursor: pointer;
    z-index: 10;
    border-radius: 50%;
}

.carousel-btn.left {
    left: 10px;
}

.carousel-btn.right {
    right: 10px;
}

.overlay {
    position: absolute;
    bottom: 10px;
    left: 50%;
    transform: translateX(-50%);
    text-align: center;
    width: 90%;
    background-color: rgba(0, 0, 0, 0.6);
    border-radius: 10px;
    padding: 10px;
    color: white;
}
</style>
