import './bootstrap.js';
import 'bootstrap/dist/css/bootstrap.min.css';
import React from "react";
import ReactDOM from "react-dom/client";
import { BrowserRouter as Router, Routes, Route } from "react-router-dom";
import FormList from "./components/FormList";
import CreateForm from "./components/CreateForm";
import EditForm from "./components/EditForm";
import FormView from "./components/FormView";
import UserTable from "./components/UserTable";
import ResponseView from "./components/ResponseView";
import "@mdi/font/css/materialdesignicons.min.css";
import './styles/app.css';

// Подключаем стили
import './styles/app.min.css';
import './styles/bootstrap.min.css';

// Подключаем JS
import 'bootstrap';


const rootElement = document.getElementById("root");

if (rootElement) {
    const root = ReactDOM.createRoot(rootElement);
    root.render(
        <Router>
            <Routes>
                <Route path="/" element={<FormList />} />
                <Route path="/forms" element={<FormList />} />
                <Route path="/users" element={<UserTable />} />
                <Route path="/forms/create" element={<CreateForm />} />
                <Route path="/form/:id" element={<FormView />} />
                <Route path="/forms/:id/edit" element={<EditForm />} />
                <Route path="/form/:id/my-response" element={<ResponseView />} />
                <Route path="/form/:id/responses/:responseId" element={<ResponseView />} />
            </Routes>
        </Router>
    );
}
