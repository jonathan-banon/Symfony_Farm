<template>
    <div v-for="animal in animals" :key="animal.id" class="animal-item rounded-3xl h-1/2 flex">
        <form class="flex justify-between w-full" @submit.prevent="editAnimal(animal)" enctype="multipart/form-data">
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
                    <img class="carousel-btn right" @click="nextImage(animal)">
                </div>

                <div class="overlay bg-opacity-50 p-2">
                    <input type="file" @change="onFileChange($event, animal)" multiple>
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
            </div>
            <div class="animal-details">
                <div class="animal-details">
                    <label for="age">Âge</label>
                    <input v-model="animal.age" type="number" id="age" class="animal-input" />
                </div>

                <div class="animal-details">
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
                    <img :src="trashUrl" alt="trash-logo" class="w-10">
                </a>
                <button type="submit" class="p-4 bg-primary font-semibold rounded-md focus:outline-none">
                    Enregistrer les modifications
                </button>
            </div>
        </form>
    </div>
</template>
<script>
export default {
    props: {
        animals: Array,
        breeds: Array,
        trashUrl: String,
    },
    emits: ['edit-animal', 'del-image', 'prev-image', 'next-image', 'on-file-change', 'upload-image', 'del-animal'],
    methods: {
        editAnimal(animal) {
            this.$emit('edit-animal', animal);
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
        onFileChange(event, animal) {
            this.$emit('on-file-change', event, animal);
        },
        uploadImage(animal) {
            this.$emit('upload-image', animal);
        },
        delAnimal(animal) {
            this.$emit('del-animal', animal);
        }
    }
};
</script>
