<template>
    <div class="flex justify-center items-center min-h-screen w-full h-full z-40 fixed bg-black"
        v-if="isLoginPopupVisible">
        <div class="w-full max-w-md bg-secondary p-8 rounded-lg shadow-lg">
            <h2 class="text-2xl font-semibold text-center text-gray-700 mb-6">Connexion</h2>
            <form @submit.prevent="handleLogin" method="POST">
                <div class="mb-4">
                    <label for="email" class="block text-sm font-medium text-gray-600">Email</label>
                    <input type="email" id="email" name="_username" v-model="email" placeholder="Entrez votre email"
                        class="w-full p-3 mt-1 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                        required />
                    <p v-if="emailError" class="text-red-500 text-sm">{{ emailError }}</p>
                </div>

                <div class="mb-6">
                    <label for="password" class="block text-sm font-medium text-gray-600">Mot de passe</label>
                    <input type="password" id="password" name="_password" v-model="password"
                        placeholder="Entrez votre mot de passe"
                        class="w-full p-3 mt-1 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                        required />
                    <p v-if="passwordError" class="text-red-500 text-sm">{{ passwordError }}</p>
                </div>

                <div class="flex justify-between">
                    <button @click="closePopUp"
                        class="w-1/3 py-3 bg-primary  font-semibold rounded-md  focus:outline-none ">
                        Retour
                    </button>
                    <button type="submit" class="w-1/2 py-3 bg-primary  font-semibold rounded-md  focus:outline-none ">
                        Se connecter
                    </button>
                </div>
            </form>
        </div>
    </div>
    <div class="bg-secondary min-h-60 flex justify-around items-center">
        <p class="alert-success bg-primary" v-if="isAlertVisible">{{ alertMessage }}</p>
        <img :src="urlLogo" alt="Logo">
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
                    <button class="btn text-white py-2 px-4 rounded-full" @click="fetchAnimals(type.id)" :class="{
                        'bg-primary': actualTypeId === type.id,
                        'bg-secondary': actualTypeId !== type.id
                    }">
                        {{ type.name }}
                    </button>
                </template>

                <template v-if="isUserLoggedIn && editingTypeId != type.id">
                    <a @click.prevent="delType(type.id)" class="delete-type-icon">
                        <img :src="trashUrl" alt="trash-logo" class="w-6">
                    </a>
                    <a @click.prevent="editType(type.id)" class="delete-type-icon">
                        <img :src="penUrl" alt="pen-logo" class="w-6">
                    </a>
                </template>
            </div>
        </div>
        <button class="btn bg-primary py-2 px-4 rounded-full" @click="toggleLoginPopup">
            {{ isUserLoggedIn ? 'Déconnexion' : 'Connexion' }}
        </button>
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
        isLoginPopupVisible: Boolean,
        actualTypeId: Number
    },
    emits: [
        'toggleLogin',
        'fetchAnimals',
        'saveEditType',
        'delType',
        'close'
    ],
    data() {
        return {
            editingTypeId: null,
            email: '',
            password: '',
            emailError: '',
            passwordError: ''
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
        },
        closePopUp() {
            this.$emit('close', false);
        },
        async handleLogin() {
            this.emailError = '';
            this.passwordError = '';

            if (!this.email) {
                this.emailError = 'L\'email est requis';
                return;
            }

            if (!this.password) {
                this.passwordError = 'Le mot de passe est requis';
                return;
            }

            try {
                const response = await fetch('http://127.0.0.1:8000/login', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify({
                        _username: this.email,
                        _password: this.password,
                    }),
                });

                const data = await response.json();

                if (response.ok) {
                    this.$emit('close', true);
                    this.$router.push(data.redirect);
                } else {
                    if (data.message) {
                        this.passwordError = data.message;
                    } else {
                        this.passwordError = 'Mot de passe ou email incorrect.';
                    }
                }
            } catch (error) {
                console.error('Erreur de connexion:', error);
                this.passwordError = 'Problème de connexion, veuillez réessayer.';
            }
        }
    }
};
</script>