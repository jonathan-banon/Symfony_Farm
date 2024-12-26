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
                    <a @click.prevent="delType(type.id)" class="delete-type-icon">
                        <img :src="trashUrl" alt="trash-logo" class="w-6">
                    </a>
                    <a @click.prevent="editType(type.id)" class="delete-type-icon">
                        <img :src="penUrl" alt="pen-logo" class="w-6">
                    </a>
                </div>
            </div>
        </div>
        <div class="h-44">
            <button class="btn bg-primary py-2 px-4 rounded-full" @click="toggleLoginPopup">
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
        'saveEditType',
        'delType',
    ],
    data() {
        return {
            editingTypeId: null,
        };
    },
    methods: {
        toggleLoginPopup() {
            this.$emit('toggleLogin');
        },
        fetchAnimals(typeId) {
            this.$emit('fetchAnimals', typeId);
        },
        editType(typeId) {
            this.editingTypeId = typeId;
        },
        saveEditType(type) {
            this.$emit('saveEditType', type);
            this.editingTypeId = null;
        },
        cancelEditType() {
            this.editingTypeId = null;
        },
        delType(id) {
            this.$emit('delType', id);
        }
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