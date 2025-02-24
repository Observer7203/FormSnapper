import './bootstrap.js';
import 'bootstrap/dist/css/bootstrap.min.css';
import React from "react";
import ReactDOM from "react-dom/client"; // для React 18
import UserTable from "./components/UserTable";
import CreateForm from "./components/CreateForm";
import FormList from "./components/FormList";
import "boxicons/css/boxicons.min.css";
import "@mdi/font/css/materialdesignicons.min.css";
import './styles/app.css';

console.log('This log comes from assets/app.js - welcome to AssetMapper! 🎉');

// Найдём root элемент в HTML
const rootElement = document.getElementById("root");

if (rootElement) {
    const root = ReactDOM.createRoot(rootElement);

    // Определяем, какую страницу рендерить
    if (window.location.pathname.startsWith("/forms")) {
        root.render(<FormList />);
    }
     else {
        root.render(<UserTable />);
    }
}
