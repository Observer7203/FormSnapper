import React, { useEffect, useState } from "react";
import axios from "axios";
import { useNavigate } from "react-router-dom";

function FormList() {
  const [forms, setForms] = useState([]);
  const navigate = useNavigate();

  useEffect(() => {
    axios.get("/api/forms")
      .then((response) => setForms(response.data))
      .catch((error) => console.error("Ошибка загрузки форм:", error));
  }, []);

  return (
    <div>
      <h2>Мои формы</h2>
      <button onClick={() => navigate("/forms/create")}>Создать форму</button>
      <ul>
        {forms.map((form) => (
          <li key={form.id}>
            {form.title} - <a href={`/forms/${form.id}`}>Просмотр</a>
          </li>
        ))}
      </ul>
    </div>
  );
}

export default FormList;
