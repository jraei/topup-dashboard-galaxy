
import { useState } from "react";
import { Plus, Edit, Trash2 } from "lucide-react";
import { Button } from "@/components/ui/button";
import DataTable from "@/components/admin/DataTable";
import CategoryForm from "@/components/admin/CategoryForm";
import { Dialog, DialogContent } from "@/components/ui/dialog";
import { 
  AlertDialog,
  AlertDialogAction,
  AlertDialogCancel,
  AlertDialogContent,
  AlertDialogDescription,
  AlertDialogFooter,
  AlertDialogHeader,
  AlertDialogTitle,
} from "@/components/ui/alert-dialog";
import { toast } from "sonner";

// Mock data for categories
const initialCategories = [
  {
    id: 1,
    name: "Mobile Games",
    slug: "mobile-games",
    description: "Top up for popular mobile games",
    icon: "fa-mobile-screen-button"
  },
  {
    id: 2,
    name: "PC Games",
    slug: "pc-games",
    description: "Credits and points for PC gaming platforms",
    icon: "fa-desktop"
  },
  {
    id: 3,
    name: "Console Games",
    slug: "console-games",
    description: "PlayStation, Xbox and Nintendo top up",
    icon: "fa-gamepad"
  },
  {
    id: 4,
    name: "Gift Cards",
    slug: "gift-cards",
    description: "Steam, iTunes, Google Play and more",
    icon: "fa-gift"
  },
  {
    id: 5,
    name: "Digital Services",
    slug: "digital-services",
    description: "Streaming, VPN and other digital services",
    icon: "fa-globe"
  }
];

const Categories = () => {
  const [categories, setCategories] = useState(initialCategories);
  const [isFormOpen, setIsFormOpen] = useState(false);
  const [isDeleteDialogOpen, setIsDeleteDialogOpen] = useState(false);
  const [selectedCategory, setSelectedCategory] = useState<any>(null);
  
  const columns = [
    {
      header: "ID",
      accessorKey: "id",
    },
    {
      header: "Name",
      accessorKey: "name",
    },
    {
      header: "Slug",
      accessorKey: "slug",
    },
    {
      header: "Description",
      accessorKey: "description",
      cell: (info: any) => (
        <div className="max-w-xs truncate">{info.description}</div>
      ),
    },
    {
      header: "Icon",
      accessorKey: "icon",
      cell: (info: any) => (
        <div className="flex items-center">
          <i className={`${info.icon} text-slate-300 mr-2`}></i>
          <span className="text-xs text-slate-400">{info.icon}</span>
        </div>
      ),
    }
  ];
  
  const handleEdit = (category: any) => {
    setSelectedCategory(category);
    setIsFormOpen(true);
  };
  
  const handleDelete = (category: any) => {
    setSelectedCategory(category);
    setIsDeleteDialogOpen(true);
  };
  
  const confirmDelete = () => {
    if (selectedCategory) {
      setCategories(categories.filter(cat => cat.id !== selectedCategory.id));
      toast.success("Category deleted successfully");
      setIsDeleteDialogOpen(false);
      setSelectedCategory(null);
    }
  };
  
  const handleFormSubmit = (data: any) => {
    if (data.id) {
      // Update existing category
      setCategories(categories.map(cat => 
        cat.id === data.id ? { ...data } : cat
      ));
    } else {
      // Create new category
      const newCategory = {
        ...data,
        id: Math.max(...categories.map(c => c.id), 0) + 1
      };
      setCategories([...categories, newCategory]);
    }
    
    setIsFormOpen(false);
    setSelectedCategory(null);
  };
  
  return (
    <div className="space-y-6">
      <div className="flex items-center justify-between">
        <h1 className="text-2xl font-bold text-slate-100">Categories</h1>
        <Button 
          onClick={() => {
            setSelectedCategory(null);
            setIsFormOpen(true);
          }}
          className="bg-topup-purple hover:bg-topup-purple/90"
        >
          <Plus size={18} className="mr-2" />
          Add Category
        </Button>
      </div>
      
      <DataTable 
        data={categories} 
        columns={columns} 
        onEdit={handleEdit}
        onDelete={handleDelete}
      />
      
      <Dialog open={isFormOpen} onOpenChange={setIsFormOpen}>
        <DialogContent className="max-w-lg bg-transparent border-0 p-0">
          <CategoryForm 
            initialData={selectedCategory || {}} 
            onSubmit={handleFormSubmit}
            onCancel={() => setIsFormOpen(false)}
          />
        </DialogContent>
      </Dialog>
      
      <AlertDialog open={isDeleteDialogOpen} onOpenChange={setIsDeleteDialogOpen}>
        <AlertDialogContent className="bg-slate-900 border border-slate-800">
          <AlertDialogHeader>
            <AlertDialogTitle className="text-slate-200">Confirm Deletion</AlertDialogTitle>
            <AlertDialogDescription className="text-slate-400">
              Are you sure you want to delete the category <span className="font-semibold text-slate-300">{selectedCategory?.name}</span>?
              <br />
              This action cannot be undone.
            </AlertDialogDescription>
          </AlertDialogHeader>
          <AlertDialogFooter>
            <AlertDialogCancel className="bg-transparent border-slate-700 text-slate-300 hover:bg-slate-800">
              Cancel
            </AlertDialogCancel>
            <AlertDialogAction
              onClick={confirmDelete}
              className="bg-red-600 hover:bg-red-700 text-white"
            >
              Delete
            </AlertDialogAction>
          </AlertDialogFooter>
        </AlertDialogContent>
      </AlertDialog>
    </div>
  );
};

export default Categories;
