<template>
    <div class="home-container flex justify-around">
        <div class="filter-container">
            <input type="text" v-model="searchVal" placeholder="Rechercher un animal par son nom" class="search-bar" />

            <select v-model="sortOrder" class="sort-select">
                <option value="price-asc">Prix Croissant</option>
                <option value="price-desc">Prix Décroissant</option>
                <option value="alpha-asc">Nom Alphabétique Croissant</option>
                <option value="alpha-desc">Nom Alphabétique Décroissant</option>
            </select>

            <div>
                <input type="radio" id="all-breeds" value="" v-model="selectedBreed" name="breed" />
                <label for="all-breeds">Toutes les races</label>
            </div>

            <div v-for="breed in breeds" :key="breed.id">
                <input type="radio" :id="breed.name" :value="breed.name" v-model="selectedBreed" name="breed" />
                <label :for="breed.name">{{ breed.name }}</label>
            </div>
        </div>

        <div class="animals-container">
            <div v-for="animal in displayedAnimals" :key="animal.id" class="animal-item rounded-3xl h-1/2 flex">
                <div class="animal-picture w-1/4 bg-primary rounded-3xl" :style="{
                    backgroundImage: 'url(' + animal.images[animal.currentImageIndex] + ')',
                    backgroundSize: 'cover',
                    backgroundPosition: 'center'
                }">
                    <div class="carousel-container" v-if="animal.images.length > 1">
                        <img class="carousel-btn left" @click="prevImage(animal)" />
                        <img class="carousel-btn right" @click="nextImage(animal)" />
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
            sortOrder: 'price-asc',
        };
    },
    computed: {
        displayedAnimals() {
            let filtered = this.animals;

            // Filtrer par nom
            if (this.searchVal) {
                const searchLower = this.searchVal.toLowerCase();
                filtered = filtered.filter((animal) =>
                    animal.name.toLowerCase().includes(searchLower)
                );
            }

            // Filtrer par race
            if (this.selectedBreed) {
                filtered = filtered.filter(
                    (animal) => animal.breed === this.selectedBreed
                );
            }

            // Trier
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
};
</script>

<style scoped>
.search-bar {
    padding: 10px;
    width: 100%;
    border-radius: 5px;
    border: 1px solid #ccc;
    margin-bottom: 20px;
}

.sort-select {
    padding: 10px;
    width: 100%;
    border-radius: 5px;
    margin-bottom: 20px;
}
</style>