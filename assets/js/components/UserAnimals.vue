<template>
    <div class="home-container flex justify-around">
        <div class="filter-container"></div>
        <div class="animals-container">
            <div v-for="animal in animals" :key="animal.id" class="animal-item rounded-3xl h-1/2 flex">
                <div class="animal-picture w-1/4 bg-primary rounded-3xl" :style="{
                    backgroundImage: 'url(' + animal.images[animal.currentImageIndex] + ')',
                    backgroundSize: 'cover',
                    backgroundPosition: 'center'
                }">
                    <div class="carousel-container" v-if="animal.images.length > 1">
                        <img class="carousel-btn left" @click="prevImage(animal)">
                        <img class="carousel-btn right" @click="nextImage(animal)">
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
        animals: Array,
    },
    emits: ['prev-image', 'next-image'],
    setup(props, { emit }) {
        const prevImage = (animal) => emit('prev-image', animal);
        const nextImage = (animal) => emit('next-image', animal);

        return {
            prevImage,
            nextImage,
        };
    },
};
</script>