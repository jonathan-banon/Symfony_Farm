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
    props: {
        actualTypeId: Number,
        showAddForm: Boolean,
        isPopupVisible: Boolean,
    },
    emits: ['close', 'fetchAnimals', 'fetchTypes', 'update:showTypeForm', 'update:isPopupVisible'],
    data() {
        return {
            newType: {
                name: '',
            },
        };
    },
    methods: {
        async addType() {
            try {
                const response = await fetch('/type/new', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify({
                        name: this.newType.name,
                    })
                });

                if (response.ok) {
                    this.$emit('fetchAnimals', this.actualTypeId);
                    this.$emit('fetchTypes');
                    this.$emit('update:showTypeForm', false);
                    this.$emit('update:isPopupVisible', false);
                } else {
                    console.error('Erreur lors de l\'ajout du type');
                }
            } catch (error) {
                console.error('Erreur lors de l\'envoi du formulaire:', error);
            }
        }
    }
};
</script>