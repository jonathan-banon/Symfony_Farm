<template>
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
        <div class="h-1/5 border-b-2 border-b-greyCustom">
            <p class="text-base">Prix</p>
            <vue-slider v-model="sliderValues" :dot-style="{ backgroundColor: '#281709' }"
                :process-style="{ backgroundColor: '#582D09' }" :use-range="true" :enable-cross="false" :max="value[1]"
                :min="value[0]"></vue-slider>
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
</template>

<script>
import VueSlider from 'vue-slider-component';
import 'vue-slider-component/theme/default.css';

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
    data() {
        return {
            searchVal: '',
            selectedBreed: null,
            sliderValues: [0, 0],
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
                if (this.animals.length === 0) return [0, 1000]; 
                const prices = this.animals.map(animal => animal.price).filter(price => price !== undefined);
                if (prices.length === 0) return [0, 1000];
                return [Math.min(...prices), Math.max(...prices)];
            },
            set(newValue) {
                this.sliderValues = newValue;
            },
        },
    },
    methods: {
        selectBreedId(breedName) {
            this.selectedBreed = breedName;
        },
    },
    watch: {
        animals(newAnimals) {
            if (newAnimals.length > 0) {
                const prices = newAnimals.map((animal) => animal.price);
                this.sliderValues = [Math.min(...prices), Math.max(...prices)];
            } else {
                this.sliderValues = [0, 0];
            }
        }
    },
};
</script>

<style>
.search-bar-container {
    height: 10%;
}

.price-container {
    border-radius: 20px;
    box-shadow: 1px 4px 1px #15151514;
    padding: 10px;
    width: fit-content;
    text-align: center;
    width: 100%;
}
</style>
