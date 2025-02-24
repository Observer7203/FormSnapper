import './bootstrap.js';
import 'bootstrap/dist/css/bootstrap.min.css';
import React from "react";
import ReactDOM from "react-dom/client";
import { BrowserRouter as Router, Routes, Route } from "react-router-dom";
import UserTable from "./components/UserTable";
import CreateForm from "./components/CreateForm";
import FormList from "./components/FormList";
import "@mdi/font/css/materialdesignicons.min.css";
import './styles/app.css';

const rootElement = document.getElementById("root");

if (rootElement) {
    const root = ReactDOM.createRoot(rootElement);
    root.render(
        <Router>
            <Routes>
                <Route path="/" element={<UserTable />} />
                <Route path="/forms" element={<FormList />} />
                <Route path="/forms/create" element={<CreateForm />} />
            </Routes>
        </Router>
    );
}
