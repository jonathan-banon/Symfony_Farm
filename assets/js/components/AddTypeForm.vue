<template>
    <form class="flex justify-between w-full" @submit.prevent="addType">
        <div>
            <div>
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