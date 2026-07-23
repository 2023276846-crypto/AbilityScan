import './bootstrap';
import { createApp } from 'vue';
import { createRouter, createWebHistory } from 'vue-router';
import App from './App.vue';
import EmployerSidebar from './components/EmployerSidebar.vue';
import PWDSidebar from './components/PWDSidebar.vue';
import AdminSidebar from './components/AdminSidebar.vue';

import HomePage from './views/Homepage.vue';
import LoginPage from './views/LoginPage.vue';
import RegisterPWD from './views/RegisterPWD.vue';
import RegisterEmployer from './views/RegisterEmployer.vue';
import PWDDashboard from './views/PWD/PWDDashboard.vue';
import ManageProfile from './views/PWD/ManageProfile.vue';
import QRPortfolio from './views/PWD/QRPortfolio.vue';
import SearchJob from './views/PWD/SearchJob.vue';
import MatchExplanation from './views/PWD/MatchExplanation.vue';
import PWDMailbox from './views/PWD/PWDMailbox.vue';
import EmployerDashboard from './views/Employer/EmployerDashboard.vue';
import PostJob from './views/Employer/PostJob.vue';
import EditJob from './views/Employer/EditJob.vue';
import CompanyProfile from './views/Employer/CompanyProfile.vue';
import ScanQR from './views/Employer/ScanQR.vue';
import SearchCandidates from './views/Employer/SearchCandidates.vue';
import EmployerMailbox from './views/Employer/EmployerMailbox.vue';
import AdminDashboard from './views/Admin/AdminDashboard.vue';
import ManagePWD from './views/Admin/ManagePWD.vue';
import ManageEmployer from './views/Admin/ManageEmployer.vue';
import GenerateReports from './views/Admin/GenerateReports.vue';
import MonitorPerformance from './views/Admin/MonitorPerformance.vue';

const routes = [
    { path: '/', component: HomePage },
    { path: '/login', component: LoginPage },
    { path: '/register/pwd', component: RegisterPWD },
    { path: '/register/employer', component: RegisterEmployer },
    { path: '/pwd/dashboard', component: PWDDashboard },
    { path: '/pwd/profile', component: ManageProfile },
    { path: '/pwd/qr', component: QRPortfolio },
    { path: '/pwd/jobs', component: SearchJob },
    { path: '/pwd/jobs/:id/explanation', component: MatchExplanation },
    { path: '/pwd/mailbox', component: PWDMailbox },
    { path: '/employer/dashboard', component: EmployerDashboard },
    { path: '/employer/post-job', component: PostJob },
    { path: '/employer/edit-job/:id', component: EditJob },
    { path: '/employer/profile', component: CompanyProfile },
    { path: '/employer/scan-qr', component: ScanQR },
    { path: '/employer/candidates', component: SearchCandidates },
    { path: '/employer/mailbox', component: EmployerMailbox },
    { path: '/admin/dashboard', component: AdminDashboard },
    { path: '/admin/pwd', component: ManagePWD },
    { path: '/admin/employers', component: ManageEmployer },
    { path: '/admin/reports', component: GenerateReports },
    { path: '/admin/monitor', component: MonitorPerformance },
];

const router = createRouter({
    history: createWebHistory(),
    routes,
});

const app = createApp(App);
app.component('EmployerSidebar', EmployerSidebar);
app.component('PWDSidebar', PWDSidebar);
app.component('AdminSidebar', AdminSidebar);
app.use(router).mount('#app');