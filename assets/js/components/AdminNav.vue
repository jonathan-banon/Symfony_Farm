<template>
    <div class="filter-container">
        <button class="p-4 bg-primary font-semibold rounded-md focus:outline-none" @click="toggleAddForm">Ajouter un
            animal</button>
     
        <button class="p-4 bg-primary font-semibold rounded-md focus:outline-none" @click="toggleBreedForm">Gérer
            mes races d'animaux</button>
    </div>
</template>
<script>
export default {
    methods: {
        toggleAddForm() {
            this.$emit('toggleAddForm');
        },
   
        toggleBreedForm() {
            this.$emit('toggleBreedForm');
        }
    }
};
</script>