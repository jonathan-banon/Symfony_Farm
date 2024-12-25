<template>
    <div id="app">
        <Navbar :types="types" :isAlertVisible="isAlertVisible" :alertMessage="alertMessage" :urlLogo="urlLogo"
            :trashUrl="trashUrl" :penUrl="penUrl" :isUserLoggedIn="isUserLoggedIn "
            :isLoginPopupVisible="isLoginPopupVisible" @toggleLogin="toggleLoginPopup" @fetchAnimals="fetchAnimals"
            @saveEditType="saveEditType" @delType="delType" @close="closeLoginPopup" :actualTypeId="actualTypeId"/>
    </div>
    <template v-if="isUserLoggedIn">
        <div class="home-container flex justify-end p-5">
            <AdminNav :showAddForm="showAddForm" :showTypeForm="showTypeForm" :showBreedForm="showBreedForm"
                @toggleAddForm="toggleAddForm" @toggleTypeForm="toggleTypeForm" @toggleBreedForm="toggleBreedForm" />
            <div class="w-3/4">
                <template v-if="showAddForm">
                    <AddAnimalForm :types="types" :breeds="breeds" @close="toggleAddForm" @addAnimal="addAnimal"
                        @fetchBreeds="fetchBreeds" />
                </template>
                <template v-if="showTypeForm">
                    <AddTypeForm @close="toggleTypeForm" @addType="addType" />
                </template>
                <template v-if="showBreedForm">
                    <BreedForm :types="types" :breeds="breeds" :trashUrl="trashUrl"
                        :penUrl="penUrl" @toggle-breed-form="toggleBreedForm" @add-breed="addBreed"
                        @fetch-breeds="fetchBreeds" @del-breed="delBreed" @edit-breed="editBreed"
                        @save-edit-breed="saveEditBreed" @cancel-edit-breed="cancelEditBreed" />
                </template>
                <AdminAnimals :animals="animals" :breeds="breeds" :trashUrl="trashUrl" @edit-animal="editAnimal"
                    @del-image="delImage" @prev-image="prevImage" @next-image="nextImage" @on-file-change="onFileChange"
                    @upload-image="uploadImage" @del-animal="delAnimal" />
            </div>
        </div>
    </template>
    <template v-else>
        <UserAnimals :animals="animals" :breeds="breeds" @prev-image="prevImage" @next-image="nextImage" />
    </template>
</template>

<script>
import logoUrl from '../../images/logo.svg';
import trashUrl from '../../images/trash.svg';
import penUrl from '../../images/pen.svg';
import Navbar from './Navbar.vue';
import AdminNav from './AdminNav.vue';
import AddAnimalForm from './AddAnimalForm.vue';
import AddTypeForm from './AddTypeForm.vue';
import BreedForm from './BreedForm.vue';
import AdminAnimals from './AdminAnimals.vue';
import UserAnimals from './UserAnimals.vue';

export default {
    components: {
        Navbar,
        AdminNav,
        AddAnimalForm,
        AddTypeForm,
        BreedForm,
        AdminAnimals,
        UserAnimals,
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
            penUrl: penUrl,
            showAddForm: false,
            showTypeForm: false,
            showBreedForm: false,
            isTypeSelected: false,
            alertMessage: "",
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
        OnTypeSelected(e) {
            console.log(e.target.value);
        },
        onFileChange(e, animal) {
            const files = e.target.files;
            if (files && files.length > 0) {
                if (!animal.files) {
                    animal.files = [];
                }
                for (let i = 0; i < files.length; i++) {
                    animal.files.push(files[i]);
                    const imageUrl = URL.createObjectURL(files[i]);
                    animal.images.push(imageUrl);
                }
                animal.currentImageIndex = animal.images.length - 1;
            }
        },
        async uploadImage(animal) {
            if (animal.files.length === 0) {
                alert("Veuillez sélectionner un fichier.");
                return;
            }

            const formData = new FormData();
            for (let i = 0; i < animal.files.length; i++) {
                formData.append("images[]", animal.files[i]);
            }
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
                    throw new Error("Échec de l'upload de l'image.");
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
        async addAnimal(newAnimal) {
            if (!newAnimal.files || newAnimal.files.length === 0) {
                alert("Veuillez sélectionner une image pour votre animal.");
                return;
            }

            const base64Files = [];

            for (let i = 0; i < newAnimal.files.length; i++) {
                const file = newAnimal.files[i];
                const reader = new FileReader();

                const fileBase64 = await new Promise((resolve, reject) => {
                    reader.onloadend = () => {
                        const base64File = reader.result.split(',')[1];
                        resolve(base64File);
                    };
                    reader.onerror = reject;
                    reader.readAsDataURL(file);
                });

                base64Files.push(fileBase64);
            }

            const response = await fetch('/animal/new', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({
                    type: newAnimal.type,
                    name: newAnimal.name,
                    breed: newAnimal.breed,
                    age: newAnimal.age,
                    description: newAnimal.description,
                    price: newAnimal.price,
                    files: base64Files,
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
        },
        async addType(newType) {
            try {
                const response = await fetch('/type/new', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify({
                        name: newType.name,
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
        async addBreed(breed) {
            try {
                const response = await fetch((`/breed/${breed.typeId}/add`), {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify({
                        name: breed.name,
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
                        status: animal.isOnSale,
                        age: animal.age
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
        editType(id) {
            this.editingTypeId = id;
        },
        async saveEditType(type) {
            this.editingTypeId = null;
            try {
                const response = await fetch(`/type/${type.id}/edit`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify({
                        name: type.name,
                    }),
                });

                if (response.ok) {
                    this.isAlertVisible = true
                    this.alertMessage = "Type d\'animal modifié avec succès"
                    setTimeout(() => {
                        this.isAlertVisible = false
                    }, 3000)

                } else {
                    console.error('Erreur lors de la modification du Type d\'animal');
                }
            } catch (error) {
                console.error('Erreur lors de l\'envoi du formulaire:', error);
            }
        },
        cancelEditType() {
            this.editingTypeId = null;
        },
        editBreed(id) {
            this.editingBreedId = id;
        },
        async delBreed(id) {
            try {
                const response = await fetch(`/breed/${id}/del`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                    }
                })
                if (response.ok) {
                    this.isAlertVisible = true;
                    this.alertMessage = "Race d'animal supprimé avec succès";
                    this.fetchAnimals(this.actualTypeId);
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
        async saveEditBreed(breed) {
            try {
                const response = await fetch(`/breed/${breed.id}/edit`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify({
                        name: breed.name,
                    }),
                });

                if (response.ok) {
                    this.editingBreedId = null
                    this.isAlertVisible = true
                    this.alertMessage = "Race d\'animal modifié avec succès"
                    setTimeout(() => {
                        this.isAlertVisible = false
                    }, 3000)

                } else {
                    console.error('Erreur lors de la modification d\'une race d\'animal');
                }
            } catch (error) {
                console.error('Erreur lors de l\'envoi du formulaire:', error);
            }
        },
        cancelEditBreed() {
            this.editingBreedId = null;
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
                    setTimeout(() => {
                        this.isAlertVisible = false
                    }, 3000)
                }
            } catch {

            }
        }
    }
}
</script>

<style>
@import '../../../assets/styles/style.css';
</style>