
import { useState } from "react";
import { X } from "lucide-react";
import { Button } from "@/components/ui/button";
import { Input } from "@/components/ui/input";
import { Label } from "@/components/ui/label";
import { Textarea } from "@/components/ui/textarea";
import { toast } from "sonner";

interface CategoryFormProps {
  initialData?: {
    id?: number;
    name: string;
    slug: string;
    description: string;
    icon: string;
  };
  onSubmit: (data: any) => void;
  onCancel: () => void;
}

const CategoryForm = ({ 
  initialData = { name: "", slug: "", description: "", icon: "" }, 
  onSubmit, 
  onCancel 
}: CategoryFormProps) => {
  const [formData, setFormData] = useState(initialData);
  const [errors, setErrors] = useState<Record<string, string>>({});
  
  const handleChange = (e: React.ChangeEvent<HTMLInputElement | HTMLTextAreaElement>) => {
    const { name, value } = e.target;
    
    // Auto-generate slug from name if slug field is empty
    if (name === "name" && (!formData.slug || formData.slug === initialData.slug)) {
      setFormData({
        ...formData,
        name: value,
        slug: value.toLowerCase().replace(/\s+/g, "-").replace(/[^a-z0-9-]/g, "")
      });
    } else {
      setFormData({ ...formData, [name]: value });
    }

    // Clear error when user starts typing
    if (errors[name]) {
      setErrors({ ...errors, [name]: "" });
    }
  };
  
  const validateForm = () => {
    const newErrors: Record<string, string> = {};
    
    if (!formData.name.trim()) {
      newErrors.name = "Name is required";
    }
    
    if (!formData.slug.trim()) {
      newErrors.slug = "Slug is required";
    } else if (!/^[a-z0-9-]+$/.test(formData.slug)) {
      newErrors.slug = "Slug can only contain lowercase letters, numbers and hyphens";
    }
    
    setErrors(newErrors);
    return Object.keys(newErrors).length === 0;
  };
  
  const handleSubmit = (e: React.FormEvent) => {
    e.preventDefault();
    
    if (validateForm()) {
      onSubmit(formData);
      toast.success(
        initialData.id ? "Category updated successfully" : "Category created successfully"
      );
    }
  };
  
  return (
    <div className="bg-slate-900 border border-slate-800 rounded-lg p-6 animate-fade-in">
      <div className="flex justify-between items-center mb-6">
        <h2 className="text-xl font-semibold text-slate-200">
          {initialData.id ? "Edit Category" : "Create New Category"}
        </h2>
        <Button 
          variant="ghost" 
          size="icon" 
          onClick={onCancel}
          className="text-slate-400 hover:text-slate-100"
        >
          <X size={18} />
        </Button>
      </div>
      
      <form onSubmit={handleSubmit} className="space-y-4">
        <div className="space-y-2">
          <Label htmlFor="name">Name</Label>
          <Input
            id="name"
            name="name"
            value={formData.name}
            onChange={handleChange}
            className="bg-slate-800 border-slate-700 text-slate-200"
            placeholder="e.g. Mobile Games"
          />
          {errors.name && <p className="text-red-500 text-sm">{errors.name}</p>}
        </div>
        
        <div className="space-y-2">
          <Label htmlFor="slug">Slug</Label>
          <Input
            id="slug"
            name="slug"
            value={formData.slug}
            onChange={handleChange}
            className="bg-slate-800 border-slate-700 text-slate-200"
            placeholder="e.g. mobile-games"
          />
          <p className="text-xs text-slate-500">
            Used in URLs: https://example.com/category/<span className="text-slate-300">{formData.slug || "slug"}</span>
          </p>
          {errors.slug && <p className="text-red-500 text-sm">{errors.slug}</p>}
        </div>
        
        <div className="space-y-2">
          <Label htmlFor="icon">Icon (URL or CSS class)</Label>
          <Input
            id="icon"
            name="icon"
            value={formData.icon}
            onChange={handleChange}
            className="bg-slate-800 border-slate-700 text-slate-200"
            placeholder="e.g. fa-gamepad or https://example.com/icons/game.svg"
          />
        </div>
        
        <div className="space-y-2">
          <Label htmlFor="description">Description</Label>
          <Textarea
            id="description"
            name="description"
            value={formData.description}
            onChange={handleChange}
            className="bg-slate-800 border-slate-700 text-slate-200 min-h-[100px]"
            placeholder="Category description (optional)"
          />
        </div>
        
        <div className="flex justify-end space-x-3 pt-4">
          <Button 
            type="button" 
            variant="outline" 
            onClick={onCancel}
            className="bg-transparent border-slate-700 text-slate-300 hover:bg-slate-800"
          >
            Cancel
          </Button>
          <Button 
            type="submit"
            className="bg-topup-purple hover:bg-topup-purple/90"
          >
            {initialData.id ? "Update Category" : "Create Category"}
          </Button>
        </div>
      </form>
    </div>
  );
};

export default CategoryForm;
