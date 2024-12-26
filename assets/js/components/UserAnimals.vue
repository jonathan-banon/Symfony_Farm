<template>
    <div class="home-container flex justify-end p-5">
        <div class="filter-container bg-gray-100  rounded-lg p-5 w-80 space-y-4 mr-5">
            <input type="text" v-model="searchVal" placeholder="Rechercher un animal par son nom"
                class="w-full p-2 rounded-md" />

            <select v-model="sortOrder" class="w-full p-2 rounded-md">
                <option value="price-asc">Trier par prix croissant</option>
                <option value="price-desc">Trier par Prix décroissant</option>
                <option value="alpha-asc">Trier par ordre alphabétique croissant</option>
                <option value="alpha-desc">Trier par ordre alphabétique décroissant</option>
                <option value="age-asc">Trier par âge croissant</option>
                <option value="age-desc">Trier par âge décroissant</option>
            </select>

            <div class="space-y-2">
                <div>
                    <input type="radio" id="all-breeds" value="" v-model="selectedBreed" name="breed" />
                    <label for="all-breeds"> Toutes les races</label>
                </div>

                <div v-for="breed in filteredBreeds" :key="breed.id" class="flex items-center space-x-2">
                    <input type="radio" :id="breed.name" :value="breed.name" v-model="selectedBreed" name="breed" />
                    <label :for="breed.name">{{ breed.name }}</label>
                </div>
            </div>

            <div class="border-t border-gray-300 pt-4 mt-4">
                <h2 class="text-lg font-semibold">Pour nous contacter :</h2>
                <p class="text-sm">Portable : 06 00 00 00 00</p>
                <p class="text-sm">Adresse : 88 Cr de Verdun, 33000 Bordeaux </p>
            </div>
        </div>

        <div class="w-3/4">
            <div v-for="animal in displayedAnimals" :key="animal.id" class="animal-item rounded-3xl h-1/2 flex">
                <div class="animal-picture w-1/4 bg-primary rounded-3xl" :style="{
                    backgroundImage: 'url(' + animal.images[animal.currentImageIndex] + ')',
                    backgroundSize: 'cover',
                    backgroundPosition: 'center'
                }">
                    <div class="carousel-container" v-if="animal.images.length > 1">
                        <img class="carousel-btn top-50 left-1" @click="prevImage(animal)" />
                        <img class="carousel-btn top-50 right-1" @click="nextImage(animal)" />
                    </div>
                </div>
                <div class="flex p-3 justify-between w-9/12">
                    <div class="flex flex-col gap-2.5">
                        <p><strong>Nom :</strong><br />{{ animal.name }}</p>
                        <p><strong>Race :</strong><br />{{ animal.breed }}</p>
                        <p><strong>Âge :</strong><br />{{ animal.age }}</p>
                        <p><strong>Description :</strong><br />{{ animal.description }}</p>
                    </div>
                    <div>
                        <p><strong>Prix :</strong><br />{{ animal.price }} €</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
export default {
    props: {
        animals: {
            type: Array,
            required: true,
        },
        breeds: {
            type: Array,
            required: true,
        },
    },
    emits: ['prev-image', 'next-image'],
    data() {
        return {
            searchVal: '',
            selectedBreed: null,
            sortOrder: 'alpha-asc',
        };
    },
    computed: {
        filteredBreeds() {
            return this.breeds.filter((breed) =>
                this.animals.some((animal) => animal.breed === breed.name)
            );
        },
        displayedAnimals() {
            let filtered = this.animals;

            if (this.searchVal) {
                const searchLower = this.searchVal.toLowerCase();
                filtered = filtered.filter((animal) =>
                    animal.name.toLowerCase().includes(searchLower)
                );
            }

            if (this.selectedBreed) {
                filtered = filtered.filter(
                    (animal) => animal.breed === this.selectedBreed
                );
            }

            return filtered.slice().sort(this.getSortFunction());
        },
    },
    methods: {
        getSortFunction() {
            switch (this.sortOrder) {
                case 'price-asc':
                    return (a, b) => a.price - b.price;
                case 'price-desc':
                    return (a, b) => b.price - a.price;
                case 'alpha-asc':
                    return (a, b) => a.name.localeCompare(b.name);
                case 'alpha-desc':
                    return (a, b) => b.name.localeCompare(a.name);
                case 'age-asc':
                    return (a, b) => a.age - b.age;
                case 'age-desc':
                    return (a, b) => b.age - a.age;
                default:
                    return () => 0;
            }
        },
        prevImage(animal) {
            this.$emit('prev-image', animal);
        },
        nextImage(animal) {
            this.$emit('next-image', animal);
        },
    },
    watch: {
        animals() {
            this.searchVal = '';
            this.selectedBreed = null;
            this.sortOrder = 'alpha-asc';
        }
    },
};
</script>
