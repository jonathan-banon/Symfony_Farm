<template>
    <div class="w-full max-w-md bg-secondary p-8 rounded-lg shadow-lg">
        <h2 class="text-2xl font-semibold text-center mb-6">Ajouter un type d'animal</h2>
        <form class="flex flex-col" @submit.prevent="addType">
            <div>
                <label for="name">Nom</label>
                <input required v-model="newType.name" type="text" id="name" class="animal-input" />
            </div>
            <div class="flex justify-between mt-5 w-full">
                <button class="p-4 bg-primary font-semibold rounded-md focus:outline-none"
                    @click="$emit('close')">Retour</button>
                <button type="submit" class="p-4 bg-primary font-semibold rounded-md focus:outline-none">
                    Ajouter le type d'animal
                </button>
            </div>
        </form>
    </div>
</template>

<script>
export default {
    name: 'TypeForm',
    emits: ['close', 'addType'],
    data() {
        return {
            newType: {
                name: '',
            },
        };
    },
    methods: {
        addType() {
            this.$emit('addType', this.newType);
            this.newType.name = '';
        }
    }
};
</script>