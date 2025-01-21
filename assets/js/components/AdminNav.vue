<template>
   
</template>

<script>
import VueSlider from 'vue-slider-component';
import 'vue-slider-component/theme/default.css';

export default {
    components: {
        VueSlider
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
