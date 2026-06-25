package com.melcomgroup.zahed.melcomshoprecount;

import com.google.gson.annotations.SerializedName;

/* JADX INFO: loaded from: classes.dex */
public class Input_values_Vview {

    @SerializedName("ITEM_CODE")
    private String ITEM_CODE;

    @SerializedName("ITEM_NAME")
    private String ITEM_NAME;

    @SerializedName("QTY")
    private String QTY;

    public String getITEM_CODE() {
        return this.ITEM_CODE;
    }

    public void setITEM_CODE(String ITEM_CODE) {
        this.ITEM_CODE = ITEM_CODE;
    }

    public String getITEM_NAME() {
        return this.ITEM_NAME;
    }

    public void setITEM_NAME(String ITEM_NAME) {
        this.ITEM_NAME = ITEM_NAME;
    }

    public String getQTY() {
        return this.QTY;
    }

    public void setQTY(String QTY) {
        this.QTY = QTY;
    }
}
