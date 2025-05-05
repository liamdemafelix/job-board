import { ref } from 'vue'
import { defineStore } from 'pinia'
import api from '@/api'

type User = {
  id: string;
  username: string;
  email: string;
  first_name: string;
  middle_name: string|null;
  last_name: string;
  is_moderator: boolean;
}

export const useAuthStore = defineStore('auth', () => {
  const user = ref<User>({} as User)
  const sessionLoading = ref<boolean>(true)
  const logoutLoading = ref<boolean>(false)
  const isAuthenticated = ref<boolean>(false)

  function checkSession() {
    api.get('/sanctum/csrf-cookie').then(() => {
      // CSRF cookie set, now we can check the session
      api.get('/api/user').then((response) => {
        user.value = response.data
        isAuthenticated.value = true
      }
      ).catch(() => {
        user.value = {} as User
        isAuthenticated.value = false
      }).finally(() => {
        sessionLoading.value = false
      })
    })
  }

  function logout() {
    logoutLoading.value = true
    api.get('/sanctum/csrf-cookie').then(() => {
      api.post('/logout').then(() => {
        user.value = {} as User
        isAuthenticated.value = false
      }).catch(() => {
        user.value = {} as User
        isAuthenticated.value = false
      }).finally(() => {
        logoutLoading.value = false
      })
    })
  }

  return {
    user,
    isAuthenticated,
    checkSession,
    logout,
    logoutLoading,
    sessionLoading
  }
})
