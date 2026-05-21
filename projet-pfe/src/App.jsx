import React from 'react'
import { BrowserRouter, Routes, Route, Navigate } from 'react-router-dom'
import { AuthProvider } from './contexts/AuthContext'
import Login from './components/auth/Login'
import Dashboard from './pages/Dashboard'
import UserList from './components/users/UserList/UserList'
import UserForm from './components/users/UserForm/UserForm'
import Layout from './components/common/Layout'
import { Toaster } from 'react-hot-toast'
import UserPage from './pages/UserPage'
import ForgetPassword from './components/auth/ForgetPassword/ForgetPassword';
import ResetPassword from './components/auth/ResetPassword/ResetPassword';

// Composant pour protéger les routes (non authentifié)
const PrivateRoute = ({ children }) => {
    const token = localStorage.getItem('auth_token')
    return token ? children : <Navigate to="/login" />
}

// Composant pour protéger les routes admin
const AdminRoute = ({ children }) => {
    const user = JSON.parse(localStorage.getItem('user') || '{}')
    return user?.role === 'administrateur' ? children : <Navigate to="/dashboard" />
}

function App() {
    return (
        <AuthProvider>
            <BrowserRouter>
                <Routes>
                    {/* Route publique */}
                    <Route path="/login" element={<Login />} />
                    <Route path="/forgot-password" element={<ForgetPassword />} />
                    <Route path="/reset-password/:token" element={<ResetPassword />} />
                    {/* Routes protégées avec Layout  comme un layouyt parent*/}
                    <Route path="/" element={
                        <PrivateRoute>
                            <Layout />
                        </PrivateRoute>
                    }>
                        {/* Dashboard est enfant de layout */}
                        <Route index element={<Navigate to="/dashboard" />} />
                        <Route path="dashboard" element={<Dashboard />} />

                        {/* Routes Admin */}
                        <Route path="utilisateurs" element={
                            <AdminRoute>
                                <UserList />
                            </AdminRoute>
                        } />
                        <Route path="utilisateurs/creer" element={
                            <AdminRoute>
                                <UserForm />
                            </AdminRoute>
                        } />
                        <Route path="utilisateurs/:id/modifier" element={
                            <AdminRoute>
                                <UserForm />
                            </AdminRoute>
                        } />
                    </Route>

                    {/* Redirection si route non trouvée */}
                    <Route path="*" element={<Navigate to="/dashboard" />} />
                </Routes>
                <Toaster position="top-right" />
            </BrowserRouter>
        </AuthProvider>
    )
}

export default App
