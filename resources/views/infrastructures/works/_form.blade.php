<div class="card mb-4">
    <div class="card-header">
        <h5 class="mb-0">Ajouter un nouveau travail</h5>
    </div>
    <div class="card-body">
        <form action="{{ route('infrastructures.works.store', $infrastructure) }}" method="POST">
            @csrf
            <div class="row">
                <div class="col-md-6">
                    <div class="mb-3">
                        <label for="work_type" class="form-label">Type de travail *</label>
                        <input type="text" class="form-control" id="work_type" name="work_type" required>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="mb-3">
                        <label for="completion_date" class="form-label">Date de réalisation *</label>
                        <input type="date" class="form-control" id="completion_date" name="completion_date" required>
                    </div>
                </div>
            </div>
            
            <div class="mb-3">
                <label for="description" class="form-label">Description</label>
                <textarea class="form-control" id="description" name="description" rows="3"></textarea>
            </div>
            
            <div class="row">
                <div class="col-md-6">
                    <div class="mb-3">
                        <label for="provider_name" class="form-label">Prestataire</label>
                        <input type="text" class="form-control" id="provider_name" name="provider_name">
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="mb-3">
                        <label for="provider_contact" class="form-label">Contact prestataire</label>
                        <input type="text" class="form-control" id="provider_contact" name="provider_contact">
                    </div>
                </div>
            </div>
            
            <div class="row">
                <div class="col-md-6">
                    <div class="mb-3">
                        <label for="cost" class="form-label">Coût (FCFA)</label>
                        <input type="number" class="form-control" id="cost" name="cost" min="0" step="0.01">
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="mb-3">
                        <label for="status" class="form-label">Statut</label>
                        <select class="form-select" id="status" name="status">
                            <option value="completed">Terminé</option>
                            <option value="in_progress">En cours</option>
                            <option value="planned">Planifié</option>
                        </select>
                    </div>
                </div>
            </div>
            
            <div class="mb-3">
                <label for="observations" class="form-label">Observations</label>
                <textarea class="form-control" id="observations" name="observations" rows="2"></textarea>
            </div>
            
            <button type="submit" class="btn btn-primary">Enregistrer le travail</button>
        </form>
    </div>
</div>
