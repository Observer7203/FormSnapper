import './bootstrap.js';
import 'bootstrap/dist/css/bootstrap.min.css';
import React from "react";
import ReactDOM from "react-dom/client";
import { BrowserRouter as Router, Routes, Route } from "react-router-dom";
import FormList from "./components/FormList";
import CreateForm from "./components/CreateForm";
import FormView from "./components/FormView";
import "@mdi/font/css/materialdesignicons.min.css";
import './styles/app.css';

const rootElement = document.getElementById("root");

if (rootElement) {
    const root = ReactDOM.createRoot(rootElement);
    root.render(
        <Router>
            <Routes>
                <Route path="/" element={<FormList />} />
                <Route path="/forms" element={<FormList />} />
                <Route path="/forms/create" element={<CreateForm />} />
                <Route path="/forms/:id" element={<FormView />} />
            </Routes>
        </Router>
    );
}
