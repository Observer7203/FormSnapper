import React, { useEffect, useState } from "react";
import axios from "axios";

function FormList() {
  const [forms, setForms] = useState([]);
  const [loading, setLoading] = useState(true);
  const [error, setError] = useState(null);

  useEffect(() => {
    axios.get("/api/forms")
      .then((response) => {
        setForms(response.data);
        setLoading(false);
      })
      .catch((error) => {
        console.error("Ошибка загрузки форм:", error);
        setError("Ошибка загрузки данных");
        setLoading(false);
      });
  }, []);

  const handleCreateForm = () => {
    window.location.href = "/forms/create";
  };

  const handleDeleteForm = async (id) => {
    if (window.confirm("Вы уверены, что хотите удалить эту форму?")) {
      try {
        await axios.delete(`/api/forms/${id}/delete`);
        setForms(forms.filter(form => form.id !== id));
      } catch (error) {
        console.error("Ошибка при удалении формы:", error);
        alert("Не удалось удалить форму.");
      }
    }
  };

  return (
    <div className="container mt-4">
      <div className="d-flex justify-content-between align-items-center mb-3">
        <h2>Мои формы</h2>
        <button className="btn btn-primary" onClick={handleCreateForm}>
          <i className="bi bi-plus-lg"></i> Создать форму
        </button>
      </div>

      {loading ? (
        <p>Загрузка...</p>
      ) : error ? (
        <p className="text-danger">{error}</p>
      ) : (
        <div className="row">
          {forms.length > 0 ? (
            forms.map((form, index) => (
              <div key={form.id} className="col-md-4 mb-4">
                <div className="card shadow-sm border-0">
                  <div className="card-body">
                    <h5 className="card-title">{form.title}</h5>
                    <p className="card-text text-muted">{form.description || "Без описания"}</p>
                    <p className="small text-secondary">
                      <i className="bi bi-ui-checks"></i> {form.questions || 0} вопросов
                    </p>
                    <div className="d-flex justify-content-between">
                      <a href={`/form/${form.id}`} className="btn btn-sm btn-outline-info">Просмотр</a>
                      <a href={`/forms/${form.id}/edit`} className="btn btn-sm btn-outline-warning">Редактировать</a>
                      <button className="btn btn-sm btn-outline-danger" onClick={() => handleDeleteForm(form.id)}>
                        Удалить
                      </button>
                    </div>
                  </div>
                </div>
              </div>
            ))
          ) : (
            <p className="text-center">Формы отсутствуют</p>
          )}
        </div>
      )}
    </div>
  );
}

export default FormList;

