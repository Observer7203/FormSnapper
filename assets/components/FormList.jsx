import React, { useEffect, useState } from "react";
import axios from "axios";

function FormList() {
  const [forms, setForms] = useState([]);

  useEffect(() => {
    axios.get("/api/forms")
      .then((response) => setForms(response.data))
      .catch((error) => console.error("Ошибка загрузки форм:", error));
  }, []);

  const handleCreateForm = () => {
    window.location.href = "/forms/create"; // Вместо useNavigate()
  };

  return (
    <div>
      <h2>Мои формы</h2>
      <button onClick={handleCreateForm}>Создать форму</button>
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
