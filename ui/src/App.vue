<script setup lang="ts">
import { RouterView, RouterLink, useRoute } from 'vue-router'
import api from '@/api'
import { CommentOutlined, FileAddOutlined, FolderOpenOutlined, LogoutOutlined, StarOutlined, UserOutlined } from '@ant-design/icons-vue'
import { useUiStore } from '@/stores/ui'
import SignInModal from '@/components/SignInModal.vue'
import RegistrationModal from '@/components/RegistrationModal.vue'
import { useAuthStore } from './stores/auth'
import { theme } from 'ant-design-vue'

const route = useRoute()
const uiStore = useUiStore()
const authStore = useAuthStore()

function logout () {
  uiStore.sidebarSelectedKeys = ['jobs']
  uiStore.sidebarOpenKeys = []
  authStore.logout()
}

// Check if there is a valid session
authStore.checkSession()

// Get keywords
api.get('/api/keywords').then((response) => {
  if (response.status === 200) {
    uiStore.keywords = response.data.data
  } else {
    console.error('Error fetching keywords:', response)
  }
})
</script>

<template>
  <a-config-provider :theme="{
    token: {
      fontFamily: 'Inter'
    },
  }">
    <a-layout>
      <a-layout-content :style="{ padding: '0 50px' }">
        <div :style="{ padding: '0', minHeight: '86vh' }">
          <a-row class="main-row" :gutter="[16, 16]">
            <a-col class="side round-l" :xs="{ span: 24, order: 2 }" :md="{ span: 9, order: 1 }" :lg="{ span: 8, order: 1 }" :xl="{ span: 7, order: 1 }" :xxl="{ span: 4, order: 1 }">
              <div class="sidebar-logo">
                <router-link :to="{ name: 'home' }" @click="uiStore.activeKeywords = []">
                  <img src="/images/logo.svg" alt="Logo" class="sidebar-logo-img" />
                </router-link>
                <div class="auth-nav" v-if="!authStore.sessionLoading">
                  <template v-if="!authStore.isAuthenticated">
                    <a-button block ghost @click="uiStore.signInModalVisible = true"><user-outlined /> Sign
                      In</a-button>
                  </template>
                </div>
              </div>
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
                  mode="inline" theme="dark" :style="{ border: 0 }">
                  <router-link to="/">
                    <a-menu-item key="jobs" @click="uiStore.sidebarSelectedKeys = ['jobs']">
                      <comment-outlined />
                      <span>Job Listings</span>
                    </a-menu-item>
                  </router-link>
                  <router-link to="/">
                    <a-menu-item key="my_jobs" @click="uiStore.sidebarSelectedKeys = ['my_jobs']"
                      v-if="authStore.user?.company != '' && authStore.user?.company != null">
                      <folder-open-outlined />
                      <span>My Job Posts</span>
                    </a-menu-item>
                  </router-link>
                  <router-link :to="{ name: 'create-job' }">
                    <a-menu-item key="new_job"
                      v-if="authStore.user?.company != '' && authStore.user?.company != null">
                        <file-add-outlined />
                        <span>Post New Job</span>
                    </a-menu-item>
                  </router-link>
                  <a-menu-item @click="logout">
                    <logout-outlined />
                    <span>Sign Out</span>
                  </a-menu-item>
                </a-menu>
              </template>
              <div class="filters pt-4 pl-4" v-if="route.name == 'home'">
                <div class="pb-2"><strong>Filters</strong></div>
                <a-checkbox-group v-model:value="uiStore.activeKeywords">
                  <a-row :gutter="[0, 4]">
                    <a-col v-for="(keyword) in uiStore.keywords" :span="24" :key="keyword.id">
                      <a-checkbox :value="keyword.name">{{ keyword.name }}</a-checkbox>
                    </a-col>
                  </a-row>
                </a-checkbox-group>
              </div>
            </a-col>
            <a-col class="main-content round-r" :xs="{ span: 24, order: 2 }" :md="{ span: 15, order: 1 }" :lg="{ span: 16, order: 1 }" :xl="{ span: 17, order: 1 }" :xxl="{ span: 20, order: 1 }">
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
  </a-config-provider>
</template>
