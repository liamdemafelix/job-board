<script setup lang="ts">
import { RouterView } from 'vue-router'
import { ApiOutlined, CommentOutlined, UserOutlined } from '@ant-design/icons-vue'
import { useUiStore } from '@/stores/ui'
import SignInModal from '@/components/SignInModal.vue'
import RegistrationModal from '@/components/RegistrationModal.vue'
import { useAuthStore } from './stores/auth'
const uiStore = useUiStore()
const authStore = useAuthStore()

// Check if there is a valid session
authStore.checkSession()
</script>

<template>
  <a-layout>
    <a-layout-header theme="light" :style="{ position: 'fixed', zIndex: 1, width: '100%' }">
      <div class="logo">
        <img src="/images/logo.svg" alt="Logo" class="logo-icon" />
      </div>
      <div class="auth-nav" v-if="!authStore.sessionLoading">
        <template v-if="authStore.isAuthenticated">
          <span class="pr-3">Hello, {{ authStore.user?.first_name }}!</span>
          <a-button :loading="authStore.logoutLoading" @click="authStore.logout" ghost><user-outlined /> Sign
            Out</a-button>
        </template>
        <template v-else>
          <a-button ghost @click="uiStore.signInModalVisible = true"><user-outlined /> Sign
            In</a-button>
        </template>
      </div>
    </a-layout-header>
  </a-layout>
  <router-view />
  <!-- Global Modals -->
  <sign-in-modal v-if="!authStore.isAuthenticated" />
  <registration-modal v-if="!authStore.isAuthenticated" />
</template>
