<template>
    <div v-if="showBreedForm" class="flex">
        <form class="flex justify-around w-full" @submit.prevent="addBreed(newBreed.typeId)">
            <div>
                <label for="type">Type</label>
                <select required v-model="newBreed.typeId" id="type" class="animal-input"
                    @change="fetchBreeds($event.target.value)">
                    <option value="" disabled selected>Sélectionnez un type</option>
                    <option v-for="type in types" :key="type.id" :value="type.id">
                        {{ type.name }}
                    </option>
                </select>
                <div class="animal-details">
                    <label for="name">Nom</label>
                    <input required v-model="newBreed.name" type="text" id="name" class="animal-input" />
                </div>
                <button class="p-4 bg-primary font-semibold rounded-md focus:outline-none"
                    @click="toggleBreedForm">Retour</button>
                <button type="submit" class="p-4 bg-primary font-semibold rounded-md focus:outline-none">
                    Ajouter une race d'animal
                </button>
            </div>
        </form>
        <div class="w-1/2 flex flex-col justify-around">
            <template v-for="breed in breeds">
                <div class="flex justify-between items-center">
                    <template v-if="editingBreedId != breed.id">
                        <p>{{ breed.name }}</p>
                        <div>
                            <a @click.prevent="delBreed(breed.id)" class="delete-type-icon">
                                <img :src="trashUrl" alt="trash-logo" class="w-6">
                            </a>
                            <a @click.prevent="editBreed(breed.id)" class="delete-type-icon">
                                <img :src="penUrl" alt="pen-logo" class="w-6">
                            </a>
                        </div>
                    </template>
                    <div v-if="editingBreedId === breed.id">
                        <input type="text" v-model="breed.name" class="border rounded px-2 py-1 w-full" />
                        <div class="flex justify-between mt-2.5">
                            <button @click="saveEditBreed(breed)"
                                class="bg-primary px-3 py-1 rounded">Enregistrer</button>
                            <button @click="cancelEditBreed" class="bg-primary px-3 py-1 rounded">Retour</button>
                        </div>
                    </div>
                </div>
            </template>
        </div>
    </div>
</template>

<script>
export default {
    props: {
        showBreedForm: Boolean,
        types: Array,
        breeds: Array,
        trashUrl: String,
        penUrl: String,
    },
    emits: [
        'toggle-breed-form',
        'add-breed',
        'fetch-breeds',
        'del-breed',
        'edit-breed',
        'save-edit-breed',
        'cancel-edit-breed'
    ],
    data() {
        return {
            newBreed: {
                name: '',
                typeId: 1,
            },
            editingBreedId: null,
        };
    },
    methods: {
        toggleBreedForm() {
            this.$emit('toggle-breed-form');
        },
        addBreed(typeId) {
            this.$emit('add-breed', { ...this.newBreed, typeId });
        },
        fetchBreeds(typeId) {
            this.$emit('fetch-breeds', typeId);
        },
        delBreed(id) {
            this.$emit('del-breed', id);
        },
        editBreed(id) {
            this.editingBreedId = id;
            this.$emit('edit-breed', id);
        },
        saveEditBreed(breed) {
            this.$emit('save-edit-breed', breed);
        },
        cancelEditBreed() {
            this.editingBreedId = null;
            this.$emit('cancel-edit-breed');
        }
    }
};
</script>
