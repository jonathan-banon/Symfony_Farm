<template>
    <div class="home-container flex xl:justify-end p-5">
        <div
            class="filter-container left-0 w-full lx:w-1/5 p-20 xl:p-5 xl:left-6 z-40 xl:z-0 bg-secondary rounded-lg p-28 w-80 space-y-4 mr-5 hidden xl:flex">
            <div class="w-[20%] h-[20px] bg-fillGrey rounded-full mx-auto mt-0 absolute top-[20px] left-[40%]"></div>
            <div class="border-b-2 border-b-greyCustom">
                <input type="text" v-model="searchVal" placeholder="Recherche ..."
                    class="w-full p-2 mb-16 rounded-2xl border border-black-500 bg-fillGrey" />
            </div>
            <div class="p-7 h-1/5 border-b-2 border-b-greyCustom">
                <p class=" mb-5 text-base">Races</p>
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
            <div class="h-1/5 border-b-2 border-b-greyCustom">
                <p class="text-base">Prix</p>
                <vue-slider v-model="sliderValues" :dot-style="{ backgroundColor: '#281709' }"
                    :process-style="{ backgroundColor: '#582D09' }" :use-range="true" :enable-cross="false"
                    :max="value[1]" :min="value[0]" @input="validateSliderValues"></vue-slider>
                <div class="flex justify-between mt-4 text-xs">
                    <div class="w-fit">
                        <p>Minimum</p>
                        <div class="price-container">
                            {{ sliderValues[0] }} €
                        </div>
                    </div>
                    <div>
                        <p>Maximum</p>
                        <div class="price-container">
                            {{ sliderValues[1] }} €
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="w-full xl:w-3/4">
            <p v-if="displayedAnimals.length != 0" class="text-xl mb-5 mt-5">{{ displayedAnimals.length }} Résultat{{
                displayedAnimals.length === 1 ? '' : 's' }} trouvé{{ displayedAnimals.length === 1 ? '' : 's' }}</p>
            <div class="flex justify-between">
                <div class="flex items-center gap-2 cursor-pointer relative mb-5" @click="toggleSortMenu">
                    <div
                        class="relative flex items-center justify-between p-2 border border-gray-300 rounded-2xl w-fit">
                        <div class="flex">
                            <img :src="logoSortBar1" class="w-4 mr-2" />
                            <p class="text-sm">{{ getSortLabel }}</p>
                        </div>
                        <img :src="logoSortBar2" class="w-4 ml-2" />
                    </div>
                </div>
                <template v-if="sortMenuOpen"
                    class="absolute bg-white border border-gray-300 p-2 rounded-md w-48 mt-2 display-sortBar">
                    <div class="bg-gray-200 p-2 mb-2 rounded-md absolute z-50 bg-secondary">
                        <div v-for="option in sortOptions" :key="option.value"
                            class="flex items-center py-1 px-2 cursor-pointer hover:bg-gray-300"
                            @click="selectSortOrder(option.value)">
                            <p class="text-sm">{{ option.label }}</p>
                        </div>
                    </div>
                </template>
                <div class="bg-white border border-gray-300 p-2 rounded-full cursor-pointer h-fit flex items-center xl:hidden"
                    @click="toggleFilter">
                    <img :src="logoFilter" class="w-4 mr-2" />
                    <p class="text-sm">Filtres</p>
                </div>
            </div>
            <div v-for="animal in displayedAnimals" :key="animal.id" class="animal-item flex">
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
                <div class="flex p-5 justify-between w-9/12">
                    <div class="flex flex-col w-9/12 gap-2.5">
                        <p class="text-4xl mt-4">{{ animal.name }} - {{ animal.age }} ans</p>
                        <p class="text-base font-light">{{ animal.breed }}</p>
                        <p class="text-base mt-4">{{ animal.description }}</p>
                    </div>
                    <div>
                        <p class="font-bold text-2xl">{{ animal.price }} €</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
import VueSlider from 'vue-slider-component'
import 'vue-slider-component/theme/default.css'
import rightArrow from '../../images/right-arrow.svg';
import leftArrow from '../../images/left-arrow.svg';
import logoSortBar1 from '../../images/logoSortBar1.svg';
import logoSortBar2 from '../../images/logoSortBar2.svg';
import logoFilter from '../../images/filter.svg';


export default {
    components: {
        VueSlider
    },
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
    emits: ['prev-image', 'next-image', 'toggleFilter'],
    data() {
        return {
            searchVal: '',
            selectedBreed: null,
            sortOrder: 'alpha-asc',
            sortMenuOpen: false,
            sliderValues: [0, 0],
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
            logoFilter: logoFilter,
        };
    },
    computed: {
        filteredBreeds() {
            return this.breeds.filter((breed) =>
                this.animals.some((animal) => animal.breed === breed.name)
            );
        },
        value: {
            get() {
                if (this.animals.length === 0) return [0, 1000]; // Plage par défaut si aucun animal
                const prices = this.animals.map(animal => animal.price).filter(price => price !== undefined);
                if (prices.length === 0) return [0, 1000]; // Plage par défaut si aucun prix valide
                return [Math.min(...prices), Math.max(...prices)];
            },
            set(newValue) {
                this.sliderValues = newValue;
            },
        },
        displayedAnimals() {
            let filtered = this.getVisibleAnimals();
            filtered = filtered.filter(
                (animal) => animal.price >= this.sliderValues[0] && animal.price <= this.sliderValues[1]
            );

            return filtered.slice().sort(this.getSortFunction());
        },
        getSortLabel() {
            const option = this.sortOptions.find(opt => opt.value === this.sortOrder);
            return option ? option.label : '';
        }
    },
    methods: {
        getVisibleAnimals() {
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
            return filtered;
        },
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
        },
        toggleFilter() {
            this.$emit('toggleFilter');
            const filterContainer = document.querySelector('.filter-container');
            if (filterContainer) {
                if (filterContainer.classList.contains('hidden')) {
                    filterContainer.classList.remove('hidden');
                    filterContainer.classList.add('flex');
                } else {
                    filterContainer.classList.remove('flex');
                    filterContainer.classList.add('hidden');
                }
            }
        },
        validateSliderValues(newValues) {
            const [min, max] = this.value;
            if (newValues[0] < min) this.sliderValues[0] = min;
            if (newValues[1] > max) this.sliderValues[1] = max;
        },
    },
    watch: {
        value(newValue) {
            if (this.sliderValues[0] < newValue[0] || this.sliderValues[1] > newValue[1]) {
                this.sliderValues = [...newValue];
            }
        },
        animals(newAnimals) {
            if (newAnimals.length > 0) {
                const prices = newAnimals.map(animal => animal.price).filter(price => price !== undefined);
                this.sliderValues = [Math.min(...prices), Math.max(...prices)];
            } else {
                this.sliderValues = [0, 1000];
            }
        },
    },
};
</script>

<style>
.price-container {
    border-radius: 20px;
    box-shadow: 1px 4px 1px #15151514;
    padding: 10px;
    width: fit-content;
    text-align: center;
    width: 100%;
}
</style>
