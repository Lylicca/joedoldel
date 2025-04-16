import { Button } from '@/components/ui/button';
import { Spinner } from '@/components/ui/spinner';
import AppLayout from '@/layouts/app-layout';
import { BlockedWord } from '@/lib/types';
import { Deferred, Head, router } from '@inertiajs/react';
import { Dialog, DialogContent, DialogDescription, DialogFooter, DialogHeader, DialogTitle } from '@/components/ui/dialog';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Pencil, Plus, Trash2 } from 'lucide-react';
import { useState } from 'react';

type PageProps = {
  words: BlockedWord[]
};

type WordFormData = {
  word: string;
  weight: number;
};

export default function BlockedWords({ words }: PageProps) {
  const [editingWord, setEditingWord] = useState<BlockedWord | null>(null);
  const [isCreateModalOpen, setIsCreateModalOpen] = useState(false);
  const [formData, setFormData] = useState<WordFormData>({
    word: '',
    weight: 100
  });

  const handleSubmit = (e: React.FormEvent) => {
    e.preventDefault();
    if (editingWord) {
      router.put(`/blocked-words/${editingWord.id}`, formData);
      setEditingWord(null);
    } else {
      router.post('/blocked-words', formData);
      setIsCreateModalOpen(false);
    }
  };

  const handleDelete = (id: number) => {
    if (confirm('Are you sure you want to delete this word?')) {
      router.delete(`/blocked-words/${id}`);
    }
  };

  const openEditModal = (word: BlockedWord) => {
    setEditingWord(word);
    setFormData({
      word: word.word,
      weight: word.weight * 100
    });
  };

  const openCreateModal = () => {
    setFormData({
      word: '',
      weight: 100
    });
    setIsCreateModalOpen(true);
  };

  return (
    <AppLayout
      breadcrumbs={[
        {
          title: 'Blocked Words',
          href: '/blocked-words',
        },
      ]}
    >
      <Head title="Blocked Words" />

      <div className="flex h-full flex-1 flex-col gap-4 rounded-xl p-4">
        <div className="flex justify-between">
          <h1 className="text-2xl font-bold">Blocked Words</h1>
          <Button onClick={openCreateModal}>
            <Plus className="mr-2 h-4 w-4" />
            Add Word
          </Button>
        </div>

        <Deferred
          data="words"
          fallback={
            <div className="grid h-full w-full place-items-center">
              <Spinner />
            </div>
          }
        >
          <div className="rounded-md border">
            <table className="w-full">
              <thead>
                <tr className="border-b bg-muted/50">
                  <th className="p-2 text-left">Word</th>
                  <th className="p-2 text-left">Weight</th>
                  <th className="p-2 text-right">Actions</th>
                </tr>
              </thead>
              <tbody>
                {words?.map((word) => (
                  <tr key={word.id} className="border-b">
                    <td className="p-2">{word.word}</td>
                    <td className="p-2">{word.weight * 100}%</td>
                    <td className="p-2 text-right">
                      <Button
                        variant="ghost"
                        size="icon"
                        onClick={() => openEditModal(word)}
                      >
                        <Pencil className="h-4 w-4" />
                      </Button>
                      <Button
                        variant="ghost"
                        size="icon"
                        className="text-destructive"
                        onClick={() => handleDelete(word.id)}
                      >
                        <Trash2 className="h-4 w-4" />
                      </Button>
                    </td>
                  </tr>
                ))}
              </tbody>
            </table>
          </div>
        </Deferred>

        <Dialog open={editingWord !== null} onOpenChange={(open) => !open && setEditingWord(null)}>
          <DialogContent>
            <DialogHeader>
              <DialogTitle>Edit Blocked Word</DialogTitle>
              <DialogDescription>
                Edit the word (all lowercased) and its spam weight. Weight should be between 0 and 100%.
              </DialogDescription>
            </DialogHeader>

            <form onSubmit={handleSubmit}>
              <div className="grid gap-4 py-4">
                <div className="grid gap-2">
                  <Label htmlFor="word">Word</Label>
                  <Input
                    id="word"
                    value={formData.word}
                    onChange={(e) => setFormData({ ...formData, word: e.target.value })}
                  />
                </div>
                <div className="grid gap-2">
                  <Label htmlFor="weight">Weight</Label>
                  <Input
                    id="weight"
                    type="number"
                    min={0}
                    max={100}
                    value={formData.weight}
                    onChange={(e) => setFormData({ ...formData, weight: parseFloat(e.target.value) })}
                  />
                </div>
              </div>
              <DialogFooter>
                <Button type="button" variant="secondary" onClick={() => setEditingWord(null)}>
                  Cancel
                </Button>
                <Button type="submit">Save changes</Button>
              </DialogFooter>
            </form>
          </DialogContent>
        </Dialog>

        <Dialog open={isCreateModalOpen} onOpenChange={setIsCreateModalOpen}>
          <DialogContent>
            <DialogHeader>
              <DialogTitle>Add Blocked Word</DialogTitle>
              <DialogDescription>
                Add a new word (all lowercased) to block with its spam weight. Weight should be between 0 and 100%.
              </DialogDescription>
            </DialogHeader>

            <form onSubmit={handleSubmit}>
              <div className="grid gap-4 py-4">
                <div className="grid gap-2">
                  <Label htmlFor="new-word">Word</Label>
                  <Input
                    id="new-word"
                    value={formData.word}
                    onChange={(e) => setFormData({ ...formData, word: e.target.value })}
                  />
                </div>
                <div className="grid gap-2">
                  <Label htmlFor="new-weight">Weight</Label>
                  <Input
                    id="new-weight"
                    type="number"
                    min={0}
                    max={100}
                    value={formData.weight}
                    onChange={(e) => setFormData({ ...formData, weight: parseFloat(e.target.value) })}
                  />
                </div>
              </div>
              <DialogFooter>
                <Button type="button" variant="secondary" onClick={() => setIsCreateModalOpen(false)}>
                  Cancel
                </Button>
                <Button type="submit">Add word</Button>
              </DialogFooter>
            </form>
          </DialogContent>
        </Dialog>
      </div>
    </AppLayout>
  );
}
