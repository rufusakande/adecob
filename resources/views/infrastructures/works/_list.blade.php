<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="mb-0">Historique des travaux</h5>
        <span class="badge bg-secondary">{{ $infrastructure->works_count }} travail(s)</span>
    </div>
    <div class="card-body">
        @if($infrastructure->works->isEmpty())
            <p class="text-muted">Aucun travail enregistré pour cette infrastructure.</p>
        @else
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>Type de travail</th>
                            <th>Date</th>
                            <th>Prestataire</th>
                            <th>Coût</th>
                            <th>Statut</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($infrastructure->works as $work)
                            <tr>
                                <td>
                                    <strong>{{ $work->work_type }}</strong>
                                    @if($work->description)
                                        <br><small class="text-muted">{{ Str::limit($work->description, 50) }}</small>
                                    @endif
                                </td>
                                <td>{{ $work->completion_date->format('d/m/Y') }}</td>
                                <td>
                                    {{ $work->provider_name ?? 'Non spécifié' }}
                                    @if($work->provider_contact)
                                        <br><small class="text-muted">{{ $work->provider_contact }}</small>
                                    @endif
                                </td>
                                <td>
                                    @if($work->cost)
                                        {{ number_format($work->cost, 0, ',', ' ') }} FCFA
                                    @else
                                        <span class="text-muted">Non défini</span>
                                    @endif
                                </td>
                                <td>
                                    <span class="badge badge-{{ $work->status == 'completed' ? 'success' : ($work->status == 'in_progress' ? 'warning' : 'info') }}">
                                        {{ $work->status == 'completed' ? 'Terminé' : ($work->status == 'in_progress' ? 'En cours' : 'Planifié') }}
                                    </span>
                                </td>
                                <td>
                                    <div class="btn-group" role="group">
                                        <button type="button" class="btn btn-sm btn-outline-primary" data-bs-toggle="modal" data-bs-target="#editWorkModal{{ $work->id }}">
                                            Modifier
                                        </button>
                                        <form action="{{ route('infrastructures.works.destroy', [$infrastructure, $work]) }}" method="POST" class="d-inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-outline-danger" onclick="return confirm('Êtes-vous sûr de vouloir supprimer ce travail ?')">
                                                Supprimer
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>
</div>

<!-- Edit Modals -->
@foreach($infrastructure->works as $work)
    <div class="modal fade" id="editWorkModal{{ $work->id }}" tabindex="-1" aria-labelledby="editWorkModalLabel{{ $work->id }}" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="editWorkModalLabel{{ $work->id }}">Modifier le travail</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="{{ route('infrastructures.works.update', [$infrastructure, $work]) }}" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="work_type_{{ $work->id }}" class="form-label">Type de travail *</label>
                                    <input type="text" class="form-control" id="work_type_{{ $work->id }}" name="work_type" value="{{ $work->work_type }}" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="completion_date_{{ $work->id }}" class="form-label">Date de réalisation *</label>
                                    <input type="date" class="form-control" id="completion_date_{{ $work->id }}" name="completion_date" value="{{ $work->completion_date->format('Y-m-d') }}" required>
                                </div>
                            </div>
                        </div>
                        
                        <div class="mb-3">
                            <label for="description_{{ $work->id }}" class="form-label">Description</label>
                            <textarea class="form-control" id="description_{{ $work->id }}" name="description" rows="3">{{ $work->description }}</textarea>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="provider_name_{{ $work->id }}" class="form-label">Prestataire</label>
                                    <input type="text" class="form-control" id="provider_name_{{ $work->id }}" name="provider_name" value="{{ $work->provider_name }}">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="provider_contact_{{ $work->id }}" class="form-label">Contact prestataire</label>
                                    <input type="text" class="form-control" id="provider_contact_{{ $work->id }}" name="provider_contact" value="{{ $work->provider_contact }}">
                                </div>
                            </div>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="cost_{{ $work->id }}" class="form-label">Coût (FCFA)</label>
                                    <input type="number" class="form-control" id="cost_{{ $work->id }}" name="cost" min="0" step="0.01" value="{{ $work->cost }}">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="status_{{ $work->id }}" class="form-label">Statut</label>
                                    <select class="form-select" id="status_{{ $work->id }}" name="status">
                                        <option value="completed" {{ $work->status == 'completed' ? 'selected' : '' }}>Terminé</option>
                                        <option value="in_progress" {{ $work->status == 'in_progress' ? 'selected' : '' }}>En cours</option>
                                        <option value="planned" {{ $work->status == 'planned' ? 'selected' : '' }}>Planifié</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        
                        <div class="mb-3">
                            <label for="observations_{{ $work->id }}" class="form-label">Observations</label>
                            <textarea class="form-control" id="observations_{{ $work->id }}" name="observations" rows="2">{{ $work->observations }}</textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                        <button type="submit" class="btn btn-primary">Mettre à jour</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endforeach
