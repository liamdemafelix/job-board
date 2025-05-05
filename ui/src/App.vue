<script setup lang="ts">
import { RouterView, RouterLink } from 'vue-router'
import { CommentOutlined, FileAddOutlined, FolderOpenOutlined, StarOutlined, UserOutlined } from '@ant-design/icons-vue'
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
        <router-link :to="{ name: 'home' }">
          <img src="/images/logo.svg" alt="Logo" class="logo-icon" />
        </router-link>
      </div>
      <div class="auth-nav" v-if="!authStore.sessionLoading">
        <template v-if="authStore.isAuthenticated">
          <a-button :loading="authStore.logoutLoading" @click="authStore.logout" ghost><user-outlined /> Sign
            Out</a-button>
        </template>
        <template v-else>
          <a-button ghost @click="uiStore.signInModalVisible = true"><user-outlined /> Sign
            In</a-button>
        </template>
      </div>
    </a-layout-header>
    <a-layout-content :style="{ padding: '0 50px', marginTop: '64px' }">
      <div :style="{ background: '#fff', padding: '24px 0', minHeight: '86vh' }">
        <a-row :gutter="[16, 16]">
          <a-col :xs="0" :md="9" :lg="8" :xl="7" :xxl="4" v-if="authStore.isAuthenticated">
            <template v-if="authStore.isAuthenticated">
              <div class="pl-4 pb-3">
                <a-row>
                  <a-col :span="6">
                    <a-avatar :size="{ xs: 0, sm: 0, md: 48, lg: 64, xl: 55, xxl: 52 }">{{
                      authStore.user?.first_name[0] }}</a-avatar>
                  </a-col>
                  <a-col :span="18" class="pt-1">
                    <a-space direction="vertical" size="small">
                      <div><strong>{{ authStore.user?.first_name }} {{ authStore.user?.last_name }}</strong></div>
                      <div><a-tag
                          :color="authStore.user?.is_moderator ? 'red' : (authStore.user?.company == '' || authStore.user?.company == null ? 'green' : 'blue')">
                          {{ authStore.user?.is_moderator ? 'Moderator' :
                            (authStore.user?.company == '' || authStore.user?.company == null ? 'Job Seeker' : 'Employer')
                          }}
                        </a-tag></div>
                    </a-space>
                  </a-col>
                </a-row>
              </div>
              <a-menu v-model:openKeys="uiStore.sidebarOpenKeys" v-model:selectedKeys="uiStore.sidebarSelectedKeys"
                mode="inline" theme="light" :style="{ border: 0 }">
                <a-menu-item key="jobs" @click="uiStore.sidebarSelectedKeys = ['jobs']">
                  <router-link to="/">
                    <comment-outlined />
                    <span>Job Listings</span>
                  </router-link>
                </a-menu-item>
                <a-menu-item key="applications" @click="uiStore.sidebarSelectedKeys = ['applications']"
                  v-if="authStore.user?.company == '' || authStore.user?.company == null">
                  <router-link to="/">
                    <star-outlined />
                    <span>My Applications</span>
                  </router-link>
                </a-menu-item>
                <a-menu-item key="my_jobs" @click="uiStore.sidebarSelectedKeys = ['my_jobs']"
                  v-if="authStore.user?.company != '' && authStore.user?.company != null">
                  <router-link to="/">
                    <folder-open-outlined />
                    <span>My Job Posts</span>
                  </router-link>
                </a-menu-item>
                <a-menu-item key="new_job" @click="uiStore.sidebarSelectedKeys = ['new_job']"
                  v-if="authStore.user?.company != '' && authStore.user?.company != null">
                  <router-link to="/">
                    <file-add-outlined />
                    <span>Post New Job</span>
                  </router-link>
                </a-menu-item>
              </a-menu>
            </template>
          </a-col>
          <a-col :xs="24" :md="15" :lg="16" :xl="17" :xxl="20" v-if="authStore.isAuthenticated">
            <router-view />
          </a-col>
          <a-col :span="24" v-else>
            <router-view />
          </a-col>
        </a-row>
      </div>
    </a-layout-content>
    <a-layout-footer :style="{ textAlign: 'center' }">
      Job Board App &copy; 2025 Liam Nicolo Demafelix
    </a-layout-footer>
  </a-layout>
  <!-- Global Modals -->
  <sign-in-modal v-if="!authStore.isAuthenticated" />
  <registration-modal v-if="!authStore.isAuthenticated" />
</template>
