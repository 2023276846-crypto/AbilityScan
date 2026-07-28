import './bootstrap';
import { createApp } from 'vue';
import { createRouter, createWebHistory } from 'vue-router';
import App from './App.vue';
import EmployerSidebar from './components/EmployerSidebar.vue';
import PWDSidebar from './components/PWDSidebar.vue';
import AdminSidebar from './components/AdminSidebar.vue';

const HomePage = () => import('./views/Homepage.vue');
const LoginPage = () => import('./views/LoginPage.vue');
const RegisterPWD = () => import('./views/RegisterPWD.vue');
const RegisterEmployer = () => import('./views/RegisterEmployer.vue');
const PWDDashboard = () => import('./views/PWD/PWDDashboard.vue');
const ManageProfile = () => import('./views/PWD/ManageProfile.vue');
const QRPortfolio = () => import('./views/PWD/QRPortfolio.vue');
const SearchJob = () => import('./views/PWD/SearchJob.vue');
const MatchExplanation = () => import('./views/PWD/MatchExplanation.vue');
const PWDMailbox = () => import('./views/PWD/PWDMailbox.vue');
const EmployerDashboard = () => import('./views/Employer/EmployerDashboard.vue');
const PostJob = () => import('./views/Employer/PostJob.vue');
const EditJob = () => import('./views/Employer/EditJob.vue');
const CompanyProfile = () => import('./views/Employer/CompanyProfile.vue');
const ScanQR = () => import('./views/Employer/ScanQR.vue');
const SearchCandidates = () => import('./views/Employer/SearchCandidates.vue');
const EmployerMailbox = () => import('./views/Employer/EmployerMailbox.vue');
const AdminDashboard = () => import('./views/Admin/AdminDashboard.vue');
const ManagePWD = () => import('./views/Admin/ManagePWD.vue');
const ManageEmployer = () => import('./views/Admin/ManageEmployer.vue');
const GenerateReports = () => import('./views/Admin/GenerateReports.vue');
const MonitorPerformance = () => import('./views/Admin/MonitorPerformance.vue');

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