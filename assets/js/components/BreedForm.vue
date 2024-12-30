<template>
    <div class="bg-secondary p-8 rounded-lg shadow-lg w-7/12">
        <h2 class="text-2xl font-semibold text-center mb-6">Gestion des races d'animaux</h2>
        <div class="flex justify-between">
            <form class="flex flex-col w-2/5" @submit.prevent="addBreed()">
                <div>
                    <label for="type">Type</label>
                    <select required v-model="newBreed.typeId" id="type" class="animal-input"
                        @change="fetchBreeds($event.target.value)">
                        <option value="" disabled selected>Sélectionnez un type</option>
                        <option v-for="type in types" :key="type.id" :value="type.id">
                            {{ type.name }}
                        </option>
                    </select>
                </div>
                <div>
                    <label for="name">Nom</label>
                    <input required v-model="newBreed.name" type="text" id="name" class="animal-input" />
                </div>
                <div class="flex justify-between mt-5">
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
                            <div class="flex gap-2.5">
                                <a @click.prevent="editBreed(breed.id)">
                                    <img :src="penUrl" alt="pen-logo" class="w-6 cursor-pointer">
                                </a>
                                <a @click.prevent="delBreed(breed.id)">
                                    <img :src="trashUrl" alt="trash-logo" class="w-6 cursor-pointer">
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
    </div>
</template>

<script>
export default {
    props: {
        actualTypeId: Number,
        types: Array,
        breeds: Array,
        trashUrl: String,
        penUrl: String,
    },
    emits: [
        'toggle-breed-form',
        'fetchAnimals',
        'fetchBreeds',
        'update:showBreedForm',
        'update:isPopupVisible',
        'update:breeds',
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
        async addBreed() {
            try {
                const response = await fetch((`/breed/${this.newBreed.typeId}/add`), {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify({
                        name: this.newBreed.name,
                    })
                });
                if (response.ok) {
                    this.$emit('fetchAnimals', this.actualTypeId);
                    this.$emit('fetchBreeds', this.actualTypeId);
                } else {
                    console.error('Erreur lors de l\'ajout');
                }
            } catch (error) {
                console.error('Erreur lors de l\'envoi du formulaire:', error);
            }
        },
        fetchBreeds(typeId) {
            this.$emit('fetchBreeds', typeId);
        },
        editBreed(id) {
            this.editingBreedId = id;
        },
        async delBreed(id) {
            try {
                const response = await fetch(`/breed/${id}/del`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                    }
                })
                if (response.ok) {
                    this.$emit('fetchAnimals', this.actualTypeId);
                    this.$emit('update:breeds', this.breeds.filter(breed => breed.id !== id));

                } else {
                    console.error('Erreur lors de la suppression du type d\'animal');
                }
            } catch {
                console.error('Erreur lors de l\'envoi du formulaire:', error);
            }
        },
        async saveEditBreed(breed) {
            try {
                const response = await fetch(`/breed/${breed.id}/edit`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify({
                        name: breed.name,
                    }),
                });

                if (response.ok) {
                    this.editingBreedId = null
                } else {
                    console.error('Erreur lors de la modification d\'une race d\'animal');
                }
            } catch (error) {
                console.error('Erreur lors de l\'envoi du formulaire:', error);
            }
        },
        cancelEditBreed() {
            this.editingBreedId = null;
        }
    }
};
</script>
