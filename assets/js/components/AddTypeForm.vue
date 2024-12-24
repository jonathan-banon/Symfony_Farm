<template>
    <form class="flex justify-between w-full" @submit.prevent="addType">
        <div>
            <div class="animal-details">
                <label for="name">Nom</label>
                <input required v-model="newType.name" type="text" id="name" class="animal-input" />
            </div>
            <button class="p-4 bg-primary font-semibold rounded-md focus:outline-none"
                @click="$emit('close')">Retour</button>
            <button type="submit" class="p-4 bg-primary font-semibold rounded-md focus:outline-none">
                Ajouter le type d'animal
            </button>
        </div>
    </form>
</template>

<script setup>
import { ref, defineEmits } from 'vue';

const emit = defineEmits(['close', 'addType']);

const newType = ref({
    name: '',
});

const addType = () => {
    emit('addType', newType.value);
    newType.value.name = ''; 
};
</script>