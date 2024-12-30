<template>
    <div class="bg-secondary min-h-60 flex justify-between items-center p-6">
        <p class="alert-success bg-primary" v-if="isAlertVisible">{{ alertMessage }}</p>
        <img class="w-60" :src="urlLogo" alt="Logo">
        <div class="nav-container">
            <div v-for="type in types" :key="type.id" class="flex">
                <template v-if="editingTypeId === type.id">
                    <div>
                        <input type="text" v-model="type.name" class="border rounded px-2 py-1 w-full" />
                        <div class="flex justify-between mt-2.5">
                            <button @click="saveEditType(type)"
                                class="bg-primary  px-3 py-1 rounded">Enregistrer</button>
                            <button @click="cancelEditType" class="bg-primary  px-3 py-1 rounded">Retour</button>
                        </div>
                    </div>
                </template>
                <template v-else>
                    <button class="type-container text-4xl" @click="fetchAnimals(type.id)" :class="{
                        'bg-primary': actualTypeId === type.id,
                        'bg-secondary border-white': actualTypeId !== type.id
                    }">
                        {{ type.name }}
                    </button>
                </template>

                <div class="flex flex-col justify-between p-1.5" v-if="isUserLoggedIn && editingTypeId != type.id">
                    <a @click.prevent="delType(type.id)">
                        <img :src="trashUrl" alt="trash-logo" class="w-6">
                    </a>
                    <a @click.prevent="editType(type.id)">
                        <img :src="penUrl" alt="pen-logo" class="w-6">
                    </a>
                </div>
            </div>
        </div>
        <div class="h-44">
            <button class="btn bg-primary py-2 px-4 rounded-full" @click="toggleLoginForm">
                {{ isUserLoggedIn ? 'Déconnexion' : 'Connexion' }}
            </button>
        </div>
    </div>
</template>
<script>

export default {
    name: 'Navbar',
    props: {
        types: Array,
        isAlertVisible: Boolean,
        alertMessage: String,
        urlLogo: String,
        trashUrl: String,
        penUrl: String,
        isUserLoggedIn: Boolean,
        actualTypeId: Number,
    },
    emits: [
        'toggleLogin',
        'fetchAnimals',
        'update:isAlertVisible',
        'update:alertMessage',
        'update:actualTypeId',
        'update:types',
    ],
    data() {
        return {
            editingTypeId: null,
        };
    },
    methods: {
        toggleLoginForm() {
            this.$emit('toggleLogin');
        },
        fetchAnimals(typeId) {
            this.$emit('fetchAnimals', typeId);
        },
        async delType(id) {
            try {
                const response = await fetch(`/type/${id}/del`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                    }
                })
                if (response.ok) {
                    this.$emit('update:isAlertVisible', true);
                    this.$emit('update:alertMessage', "Type d'animal supprimé avec succès");

                    const data = await response.json();
                    if (data.newTypeId) {
                        this.$emit('update:actualTypeId', data.newTypeId);
                        this.$emit('fetchAnimals', this.actualTypeId);
                    }
                    this.$emit('update:types', this.types.filter(type => type.id !== id));

                    setTimeout(() => {
                        this.$emit('update:isAlertVisible', false);
                    }, 3000)

                } else {
                    console.error('Erreur lors de la suppression du type d\'animal');
                }
            } catch {
                console.error('Erreur lors de l\'envoi du formulaire:', error);
            }
        },
        editType(id) {
            this.editingTypeId = id;
        },
        async saveEditType(type) {
            this.editingTypeId = null;
            try {
                const response = await fetch(`/type/${type.id}/edit`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify({
                        name: type.name,
                    }),
                });

                if (response.ok) {
                    this.$emit('update:isAlertVisible', true);
                    this.$emit('update:alertMessage', "Type d\'animal modifié avec succès");

                    setTimeout(() => {
                        this.$emit('update:isAlertVisible', false);
                    }, 3000)

                } else {
                    console.error('Erreur lors de la modification du Type d\'animal');
                }
            } catch (error) {
                console.error('Erreur lors de l\'envoi du formulaire:', error);
            }
        },
        cancelEditType() {
            this.editingTypeId = null;
        },
    }
};
</script>
<style>
.nav-container {
    display: flex;
    min-width: 50%;
    justify-content: space-around;
    align-items: end;
}

.alert-success {
    position: fixed;
    top: 15%;
    right: 15px;
    width: 20%;
    border-radius: 10px;
    padding: 10px;
    text-align: center;
    font-weight: bold;
}

.type-container {
    width: fit-content;
    border-radius: 20px;
    padding: 30px;
}

.border-white {
    border: 2px solid white;
}
</style>