<template>
    <div class="home-container flex justify-end p-5">
        <div class="filter-container-user">
            <input type="text" v-model="searchVal" placeholder="Rechercher un animal par son nom" class="search-bar" />

            <select v-model="sortOrder" class="sort-select">
                <option value="price-asc">Prix Croissant</option>
                <option value="price-desc">Prix Décroissant</option>
                <option value="alpha-asc">Nom Alphabétique Croissant</option>
                <option value="alpha-desc">Nom Alphabétique Décroissant</option>
                <option value="age-asc">Âge Croissant</option>
                <option value="age-desc">Âge Décroissant</option>
            </select>
            <div>
                <div>
                    <input type="radio" id="all-breeds" value="" v-model="selectedBreed" name="breed" />
                    <label for="all-breeds">Toutes les races</label>
                </div>

                <div v-for="breed in breeds" :key="breed.id">
                    <input type="radio" :id="breed.name" :value="breed.name" v-model="selectedBreed" name="breed" />
                    <label :for="breed.name">{{ breed.name }}</label>
                </div>
            </div>
            <div>
                <h2>Pour nous contacter :</h2>
                <p>Portable : 06 00 00 00 00</p>
                <p>Adresse : 88 Cr de Verdun, 33000 Bordeaux </p>
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
                        <img class="carousel-btn left" @click="prevImage(animal)" />
                        <img class="carousel-btn right" @click="nextImage(animal)" />
                    </div>
                </div>
                <div class="flex p-3 justify-between w-9/12">
                    <div>
                        <p><strong>Nom :</strong><br/>{{ animal.name }}</p>
                        <p><strong>Race :</strong><br/>{{ animal.breed }}</p>
                        <p><strong>Âge :</strong><br/>{{ animal.age }}</p>
                        <p><strong>Description :</strong><br/>{{ animal.description }}</p>
                    </div>
                    <div>
                        <p><strong>Prix :</strong><br/>{{ animal.price }} €</p>
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

.filter-container-user {
    width: 20vw;
    height: 65vh;
    display: flex;
    flex-direction: column;
    justify-content: space-around;
    padding: 20px;
    background-color: #f1f1f1;
    border-radius: 20px;
    position: fixed;
    left: 20px;
}
</style>