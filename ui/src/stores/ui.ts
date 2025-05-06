import { reactive, ref } from 'vue'
import { useRouter } from 'vue-router'
import { defineStore } from 'pinia'
import { notification } from 'ant-design-vue'
import { useAuthStore } from '@/stores/auth'
import api from '@/api'

export const useUiStore = defineStore('ui', () => {
  const router = useRouter()
    const selectedNavKeys = ref<string[]>([''])
    const sidebarOpenKeys = ref<string[]>([''])
    const sidebarSelectedKeys = ref<string[]>(['jobs'])
    const signInModalVisible = ref<boolean>(false)
    const registerModalVisible = ref<boolean>(false)
    const signInLoading = ref<boolean>(false)
    const registerLoading = ref<boolean>(false)
    const registrationErrors = ref<string[]>([])
    const accountTypes = reactive(['Job Seeker', 'Employer'])
    const keywords = ref<any>()
    const activeKeywords = ref<string[]>([])

    // Undo this after deployment
    const signInForm = ref<{ email: string; password: string }>({
        email: 'liamdemafelix',
        password: 'P@ssw0rd'
    })

    const registrationForm = ref<{
        email: string;
        password: string;
        password_confirmation: string;
        first_name: string;
        middle_name: string | null;
        last_name: string;
        company_name: string | null;
        username: string;
        type: string;
    }>({
        email: '',
        password: '',
        password_confirmation: '',
        first_name: '',
        middle_name: null,
        last_name: '',
        company_name: null,
        username: '',
        type: 'Job Seeker'
    })

    function closeSignInModal() {
      // Undo this after deployment
      signInForm.value.email = 'liamdemafelix'
      signInForm.value.password = 'P@ssw0rd'
      signInLoading.value = false
      signInModalVisible.value = false
    }

    function closeRegistrationModal() {
      registrationForm.value.email = ''
      registrationForm.value.password = ''
      registrationForm.value.password_confirmation = ''
      registrationForm.value.first_name = ''
      registrationForm.value.middle_name = null
      registrationForm.value.last_name = ''
      registrationForm.value.company_name = null
      registrationForm.value.username = ''
      registrationForm.value.type = 'Job Seeker' // seeker by default
      registerModalVisible.value = false
      registerLoading.value = false
    }

    function showRegistrationModal() {
      closeSignInModal()
      registerModalVisible.value = true
    }

    function showSignInModal() {
      closeRegistrationModal()
      signInModalVisible.value = true
    }

    async function signIn() {
      const authStore = useAuthStore()
      signInLoading.value = true
      api.get('/sanctum/csrf-cookie').then(() => {
        api.post('/login', signInForm.value).then(() => {
          closeSignInModal()
          authStore.checkSession()
        }).catch((err) => {
          notification['error']({
            message: 'Error',
            description: 'You have entered an invalid email or password.',
          })
          signInLoading.value = false
        }).finally(() => {
            router.push('/?s=' + Math.random().toString(36).substring(2))
        })
      })
    }

    async function register() {
      const authStore = useAuthStore()
      registerLoading.value = true
      api.get('/sanctum/csrf-cookie').then(() => {
        api.post('/register', registrationForm.value).then(() => {
          closeRegistrationModal()
          authStore.checkSession()
          notification['success']({
            message: 'Success',
            description: 'Registration successful.',
          })
          registrationErrors.value = []
        }).catch((err) => {
          registrationErrors.value = []
          if (err.response.data.errors) {
            // Loop through the errors and push them to the registrationErrors array
            const errors = err.response.data.errors
            for (const key in errors) {
              if (errors[key] && errors[key].length > 0) {
                registrationErrors.value.push(errors[key][0])
              }
            }
          }
          registerLoading.value = false
        }).finally(() => {
          router.push('/?s=' + Math.random().toString(36).substring(2))
        })
      })
    }

    return {
      selectedNavKeys,
      sidebarOpenKeys,
      sidebarSelectedKeys,
      signInModalVisible,
      registerModalVisible,
      signInLoading,
      closeSignInModal,
      showRegistrationModal,
      signInForm,
      signIn,
      registerLoading,
      closeRegistrationModal,
      registrationForm,
      showSignInModal,
      register,
      registrationErrors,
      accountTypes,
      keywords,
      activeKeywords
    }
  })
