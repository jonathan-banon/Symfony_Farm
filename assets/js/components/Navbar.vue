<template>
    <div class="bg-secondary flex items-center shadow-lg">
        <div class="flex w-1/4 pl-4">
            <img class="w-16" :src="urlLogo" alt="Logo">
            <img class="w-36 ml-11" :src="textLogoUrl" alt="textLogoUrl">
        </div>
        <div class="nav-container w-3/5">
            <div v-for="type in types" :key="type.id" class="flex h-full w-1/6">
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
                    <button class="p-5 text-4xl w-full" @click="fetchAnimals(type.id)" :class="{
                        'shadow-inner shadow-black': actualTypeId === type.id,
                        'bg-secondary border-white': actualTypeId !== type.id
                    }">
                        {{ type.name }}
                    </button>
                </template>

                <div class="flex flex-col justify-between p-1.5" v-if="isUserLoggedIn && editingTypeId != type.id">
                    <a @click.prevent="delType(type.id)">
                        <img :src="trashUrl" alt="trash-logo" class="w-6 cursor-pointer">
                    </a>
                    <a @click.prevent="editType(type.id)">
                        <img :src="penUrl" alt="pen-logo" class="w-6 cursor-pointer">
                    </a>
                </div>
            </div>
        </div>
        <div class="flex justify-end pr-4 w-1/6">
            <button class="btn bg-primary text-secondary py-2 px-4 rounded-full" @click="toggleLoginForm">
                {{ isUserLoggedIn ? 'Déconnexion' : 'Back-office' }}
            </button>
        </div>
    </div>
</template>
<script>

export default {
    name: 'Navbar',
    props: {
        types: Array,
        urlLogo: String,
        textLogoUrl: String,
        trashUrl: String,
        penUrl: String,
        isUserLoggedIn: Boolean,
        actualTypeId: Number,
    },
    emits: [
        'toggleLogin',
        'fetchAnimals',
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

                    const data = await response.json();
                    if (data.newTypeId) {
                        this.$emit('update:actualTypeId', data.newTypeId);
                        this.$emit('fetchAnimals', this.actualTypeId);
                    }
                    this.$emit('update:types', this.types.filter(type => type.id !== id));
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
    justify-content: start;
    align-items: end;
    gap: 20px;
    height: 10vh;
}

.border-white {
    border: 2px solid white;
}
</style>