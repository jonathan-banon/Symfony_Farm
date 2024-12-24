<template>
    <div class="filter-container">
        <div v-if="!showAddForm && !showTypeForm && !showBreedForm" class="flex justify-around">
            <button class="p-4 bg-primary font-semibold rounded-md focus:outline-none" @click="toggleAddForm">Ajouter un
                animal</button>
            <button class="p-4 bg-primary font-semibold rounded-md focus:outline-none" @click="toggleTypeForm">Ajouter
                un Type d'animal</button>
            <button class="p-4 bg-primary font-semibold rounded-md focus:outline-none" @click="toggleBreedForm">Gérer
                mes races d'animaux</button>
        </div>
    </div>
</template>
<script>
export default {
    props: {
        showAddForm: {
            type: Boolean,
            required: true
        },
        showTypeForm: {
            type: Boolean,
            required: true
        },
        showBreedForm: {
            type: Boolean,
            required: true
        }
    },
    methods: {
        toggleAddForm() {
            this.$emit('toggleAddForm');
        },
        toggleTypeForm() {
            this.$emit('toggleTypeForm');
        },
        toggleBreedForm() {
            this.$emit('toggleBreedForm');
        }
    }
};
</script>