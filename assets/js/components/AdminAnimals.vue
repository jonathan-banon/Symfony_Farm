<template>
    <div v-for="animal in animals" :key="animal.id" class="animal-item rounded-3xl h-1/2 flex">
        <form class="flex justify-between w-full" @submit.prevent="editAnimal(animal)" enctype="multipart/form-data">
            <div class="animal-picture w-1/4 bg-primary rounded-3xl" :style="{
                backgroundImage: 'url(' + animal.images[animal.currentImageIndex] + ')',
                backgroundSize: 'cover',
                backgroundPosition: 'center'
            }">
                <div class="flex justify-end p-3">
                    <a @click.prevent="delImage(animal)">
                        <img :src="trashUrl" alt="trash-logo" class="w-10 cursor-pointer">
                    </a>
                </div>
                <div class="carousel-container" v-if="animal.images.length > 1">
                    <img :src="leftArrow" class="carousel-btn top-30 left-1 w-10" @click="prevImage(animal)">
                    <img :src="rightArrow" class="carousel-btn top-30 right-1 w-10" @click="nextImage(animal)">
                </div>

                <div class="overlay bg-opacity-50 p-2">
                    <input type="file" @change="onFileChange($event, animal)" multiple>
                    <button @click="uploadImage(animal)">Télécharger</button>
                </div>
            </div>
            <div>
                <div>
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

                <div>
                    <label for="description">Description</label>
                    <textarea v-model="animal.description" id="description" class="animal-input"></textarea>
                </div>
            </div>
            <div>
                <div>
                    <label for="age">Âge</label>
                    <input v-model="animal.age" type="number" id="age" class="animal-input" />
                </div>

                <div>
                    <label for="price">Prix</label>
                    <input v-model="animal.price" type="number" id="price" class="animal-input" />
                </div>
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
                    <img :src="trashUrl" alt="trash-logo" class="w-10 cursor-pointer">
                </a>
                <button type="submit" class="p-4 bg-primary font-semibold rounded-md focus:outline-none">
                    Enregistrer les modifications
                </button>
            </div>
        </form>
    </div>
</template>
<script>
import rightArrow from '../../images/right-arrow.svg';
import leftArrow from '../../images/left-arrow.svg';

export default {
    data() {
        return {
            leftArrow: leftArrow,
            rightArrow: rightArrow,
        }

    },
    props: {
        animals: Array,
        breeds: Array,
        trashUrl: String,
    },
    emits: ['del-image', 'prev-image', 'next-image', 'del-animal', 'update:animals'],
    methods: {
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
            } catch (error) {
                console.error('Erreur lors de l\'envoi du formulaire:', error);
            }
        },
        delImage(animal) {
            this.$emit('del-image', animal);
        },
        prevImage(animal) {
            this.$emit('prev-image', animal);
        },
        nextImage(animal) {
            this.$emit('next-image', animal);
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
                    this.$emit('update:animals', this.animals.filter(a => a.id !== animal.id))
                } else {
                    console.error('Erreur lors de la suppression de l\'animal');
                }
            } catch {
                console.error('Erreur lors de l\'envoi du formulaire:', error);
            }
        },
    }
};
</script>

<style>
.top-30 {
    top: 30% !important;
}
</style>
