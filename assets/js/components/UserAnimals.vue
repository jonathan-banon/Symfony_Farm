<template>
    <div class="home-container flex justify-end p-5">
        <div class="filter-container bg-gray-100 rounded-lg p-5 w-80 space-y-4 mr-5">
            <div class="search-bar-container border-b-2 border-b-greyCustom">
                <input type="text" v-model="searchVal" placeholder="Recherche ..."
                    class="w-full p-2 rounded-2xl border-2 border-black-500 bg-fillGrey" />
            </div>

            <div class="h-1/5 border-b-2 border-b-greyCustom">
                <p class="text-base">Races</p>
                <div class="flex gap-3 mt-2">
                    <button class="text-xs border border-primary-500 p-2 rounded-full" :class="{
                        'bg-primary text-secondary': selectedBreed === null,
                        'bg-transparent text-primary-500': selectedBreed !== null
                    }" @click="selectBreedId(null)">
                        Toutes les races
                    </button>

                    <template v-for="breed in filteredBreeds" :key="breed.id">
                        <button class="text-xs border border-primary-500 p-2 rounded-full" :class="{
                            'bg-primary text-secondary': selectedBreed === breed.name,
                            'bg-transparent text-primary-500': selectedBreed !== breed.name
                        }" @click="selectBreedId(breed.name)">
                            {{ breed.name }}
                        </button>
                    </template>
                </div>
            </div>

            <div class="border-t border-gray-300 pt-4 mt-4">
                <h2 class="text-lg font-semibold">Pour nous contacter :</h2>
                <p class="text-sm">Portable : 06 00 00 00 00</p>
                <p class="text-sm">Adresse : 88 Cr de Verdun, 33000 Bordeaux </p>
            </div>
        </div>

        <div class="w-3/4">
            <p v-if="displayedAnimals.length != 0" class="text-xs mb-5 mt-5">{{ displayedAnimals.length }} Résultat{{ displayedAnimals.length === 1 ? '' : 's' }} trouvé{{
                displayedAnimals.length === 1 ? '' : 's' }}</p>
            <div class="flex items-center gap-2 cursor-pointer relative mb-5" @click="toggleSortMenu">
                <div class="relative flex items-center justify-between p-2 border border-gray-300 rounded-2xl w-4/12">
                    <div class="flex">
                        <img :src="logoSortBar1" class="w-4 mr-2" />
                        <p class="text-sm">{{ getSortLabel }}</p>
                    </div>
                    <img :src="logoSortBar2" class="w-4 ml-2" />
                </div>
            </div>
            <template v-if="sortMenuOpen"
                class="absolute bg-white border border-gray-300 p-2 rounded-md w-48 mt-2 display-sortBar">
                <div class="bg-gray-200 p-2 mb-2 rounded-md">
                    <div v-for="option in sortOptions" :key="option.value"
                        class="flex items-center py-1 px-2 cursor-pointer hover:bg-gray-300"
                        @click="selectSortOrder(option.value)">
                        <p class="text-sm">{{ option.label }}</p>
                    </div>
                </div>
            </template>

            <div v-for="animal in displayedAnimals" :key="animal.id" class="animal-item rounded-3xl h-1/2 flex">
                <div class="animal-picture w-1/4 bg-primary rounded-3xl" :style="{
                    backgroundImage: 'url(' + animal.images[animal.currentImageIndex] + ')',
                    backgroundSize: 'cover',
                    backgroundPosition: 'center'
                }">
                    <div class="carousel-container" v-if="animal.images.length > 1">
                        <img :src="leftArrow" class="carousel-btn top-50 left-1 w-10" @click="prevImage(animal)" />
                        <img :src="rightArrow" class="carousel-btn top-50 right-1 w-10" @click="nextImage(animal)" />
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
import rightArrow from '../../images/right-arrow.svg';
import leftArrow from '../../images/left-arrow.svg';
import logoSortBar1 from '../../images/logoSortBar1.svg';
import logoSortBar2 from '../../images/logoSortBar2.svg';

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
            sortMenuOpen: false,
            sortOptions: [
                { value: 'price-asc', label: 'Trier par prix croissant' },
                { value: 'price-desc', label: 'Trier par prix décroissant' },
                { value: 'alpha-asc', label: 'Trier par ordre alphabétique croissant' },
                { value: 'alpha-desc', label: 'Trier par ordre alphabétique décroissant' },
                { value: 'age-asc', label: 'Trier par âge croissant' },
                { value: 'age-desc', label: 'Trier par âge décroissant' },
            ],
            rightArrow: rightArrow,
            leftArrow: leftArrow,
            logoSortBar1: logoSortBar1,
            logoSortBar2: logoSortBar2,
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
        getSortLabel() {
            const option = this.sortOptions.find(opt => opt.value === this.sortOrder);
            return option ? option.label : '';
        }
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
        selectBreedId(breedName) {
            this.selectedBreed = breedName;
        },
        toggleSortMenu() {
            this.sortMenuOpen = !this.sortMenuOpen;
        },
        selectSortOrder(value) {
            this.sortOrder = value;
            this.sortMenuOpen = false;
        }
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

<style>
.search-bar-container {
    height: 10%;
}

.display-sortBar {
    z-index: 100;
    background-color: blue;
    width: 20%;
}
</style>
