<template>
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

            <div>
                <button type="submit"
                    class="w-full py-3 bg-primary  font-semibold rounded-md  focus:outline-none ">
                    Se connecter
                </button>
            </div>
        </form>
    </div>
</template>

<script>
export default {
    props: ['isVisible'],
    data() {
        return {
            email: '',
            password: '',
            emailError: '',
            passwordError: ''
        };
    },
    methods: {
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
                    this.$emit('close');
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

<style scoped></style>
